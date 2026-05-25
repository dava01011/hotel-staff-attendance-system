class FaceRecognitionHandler {
    constructor() {
        this.video = null;
        this.canvas = null;
        this.stream = null;
        this.modelsLoaded = false;
    }

    /**
     * Load face-api.js models
     */
    async loadModels() {
        try {
            const MODEL_URL = '/models'; // Pastikan folder models ada di public

            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            ]);

            this.modelsLoaded = true;
            console.log('Face recognition models loaded successfully');
            return true;
        } catch (error) {
            console.error('Error loading models:', error);
            return false;
        }
    }

    /**
     * Start camera
     */
    async startCamera(videoElement) {
        try {
            this.video = videoElement;

            this.stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user'
                },
                audio: false
            });

            this.video.srcObject = this.stream;

            return new Promise((resolve) => {
                this.video.onloadedmetadata = () => {
                    this.video.play();
                    resolve(true);
                };
            });
        } catch (error) {
            console.error('Error accessing camera:', error);
            throw new Error('Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.');
        }
    }

    /**
     * Stop camera
     */
    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        if (this.video) {
            this.video.srcObject = null;
        }
    }

    /**
     * Detect face and get descriptor
     */
    async detectFace() {
        if (!this.modelsLoaded) {
            throw new Error('Models belum di-load');
        }

        if (!this.video) {
            throw new Error('Video belum diinisialisasi');
        }

        try {
            const detection = await faceapi
                .detectSingleFace(this.video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                throw new Error('Wajah tidak terdeteksi. Pastikan wajah Anda berada di tengah kamera.');
            }

            return detection.descriptor;
        } catch (error) {
            console.error('Error detecting face:', error);
            throw error;
        }
    }

    /**
     * Draw detection box on canvas
     */
    drawDetection(canvas, detection) {
        if (!detection) return;

        const displaySize = {
            width: this.video.videoWidth,
            height: this.video.videoHeight
        };

        faceapi.matchDimensions(canvas, displaySize);

        const resizedDetections = faceapi.resizeResults(detection, displaySize);

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        faceapi.draw.drawDetections(canvas, resizedDetections);
        faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
    }

    /**
     * Start real-time face detection overlay
     */
    async startFaceDetectionOverlay(videoElement, canvasElement) {
        this.canvas = canvasElement;
        await this.startCamera(videoElement);

        const detectFace = async () => {
            if (!this.video || this.video.paused) return;

            const detection = await faceapi
                .detectSingleFace(this.video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            this.drawDetection(this.canvas, detection);
            requestAnimationFrame(detectFace);
        };

        detectFace();
    }
}

// Global instance
const faceRecognition = new FaceRecognitionHandler();

/**
 * Absensi Handler
 */
class AbsensiHandler {
    constructor() {
        this.latitude = null;
        this.longitude = null;
        this.faceDescriptor = null;
    }

    /**
     * Get current location
     */
    async getLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation tidak didukung oleh browser Anda'));
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.latitude = position.coords.latitude;
                    this.longitude = position.coords.longitude;
                    resolve({
                        latitude: this.latitude,
                        longitude: this.longitude
                    });
                },
                (error) => {
                    let message = 'Tidak dapat mendapatkan lokasi';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Izin akses lokasi ditolak. Aktifkan GPS dan berikan izin.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Informasi lokasi tidak tersedia';
                            break;
                        case error.TIMEOUT:
                            message = 'Waktu permintaan lokasi habis';
                            break;
                    }
                    reject(new Error(message));
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }

    /**
     * Calculate distance between two coordinates
     */
    calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Radius bumi dalam meter
        const dLat = this.toRad(lat2 - lat1);
        const dLon = this.toRad(lon2 - lon1);

        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(this.toRad(lat1)) * Math.cos(this.toRad(lat2)) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = R * c;

        return Math.round(distance);
    }

    toRad(value) {
        return value * Math.PI / 180;
    }

    /**
     * Perform absensi masuk
     */
    async absensiMasuk() {
        try {
            // Show loading
            this.showLoading('Memproses absensi...');

            // Get location
            const location = await this.getLocation();

            // Get face descriptor
            const descriptor = await faceRecognition.detectFace();
            this.faceDescriptor = Array.from(descriptor);

            // Send to server
            const response = await fetch('/karyawan/absensi/masuk', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    latitude: location.latitude,
                    longitude: location.longitude,
                    face_descriptor: JSON.stringify(this.faceDescriptor)
                })
            });

            const result = await response.json();

            this.hideLoading();

            if (result.success) {
                this.showSuccess(result.message);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            this.hideLoading();
            this.showError(error.message);
        }
    }

    /**
     * Perform absensi pulang
     */
    async absensiPulang() {
        try {
            // Show loading
            this.showLoading('Memproses absensi...');

            // Get location
            const location = await this.getLocation();

            // Get face descriptor
            const descriptor = await faceRecognition.detectFace();
            this.faceDescriptor = Array.from(descriptor);

            // Send to server
            const response = await fetch('/karyawan/absensi/pulang', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    latitude: location.latitude,
                    longitude: location.longitude,
                    face_descriptor: JSON.stringify(this.faceDescriptor)
                })
            });

            const result = await response.json();

            this.hideLoading();

            if (result.success) {
                this.showSuccess(result.message);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            this.hideLoading();
            this.showError(error.message);
        }
    }

    // UI Helper methods
    showLoading(message) {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading-overlay';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        loadingDiv.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-700">${message}</p>
            </div>
        `;
        document.body.appendChild(loadingDiv);
    }

    hideLoading() {
        const loadingDiv = document.getElementById('loading-overlay');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }

    showSuccess(message) {
        this.showAlert(message, 'success');
    }

    showError(message) {
        this.showAlert(message, 'error');
    }

    showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white max-w-md`;
        alertDiv.textContent = message;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Global instance
const absensiHandler = new AbsensiHandler();

// Export untuk digunakan di HTML
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { FaceRecognitionHandler, AbsensiHandler };
}
