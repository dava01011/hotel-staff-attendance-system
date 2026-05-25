<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Wajah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Registrasi Wajah - <span id="karyawanName">{{ $karyawan->nama ?? 'Test User' }}</span></h4>
            <p class="text-muted mb-0">{{ $karyawan->departemen ?? 'Departemen' }} - {{ $karyawan->nik ?? 'NIK' }}</p>
        </div>
        <div class="card-body">
            <!-- Debug Log -->
            <div class="alert alert-secondary small mb-3" style="max-height: 120px; overflow-y: auto;">
                <strong>🔍 Debug Log:</strong>
                <div id="logContent" style="font-family: monospace; font-size: 11px;">Initializing...</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="video-container position-relative bg-dark rounded" style="height: 360px;">
                        <video id="video" width="100%" height="360" autoplay playsinline muted class="rounded"></video>
                        <canvas id="overlay" style="position:absolute;top:0;left:0;pointer-events:none;"></canvas>
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong> Instruksi:</strong>
                        <ul class="mb-0 small">
                            <li>Pastikan pencahayaan cukup terang </li>
                            <li>Posisikan wajah di tengah frame</li>
                            <li>Jarak ideal 50-80cm dari kamera</li>
                            <li>Tatap kamera dengan wajah lurus</li>
                        </ul>
                    </div>

                    <div id="status" class="alert d-none mt-3"></div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5> Status Deteksi</h5>
                            <div class="mb-3">
                                <span class="badge bg-secondary" id="detectionBadge">⏳ Menunggu...</span>
                            </div>

                            <div id="faceInfo" class="d-none">
                                <p class="mb-1"><strong>Kecocokan:</strong> <span id="confidence">-</span></p>
                                <p class="mb-1"><strong>Ukuran:</strong> <span id="faceSize">-</span></p>
                                <p class="mb-0"><strong>Deteksi:</strong> <span id="detectionCount">0</span> frames</p>
                            </div>

                            <div id="capturedPreview" class="d-none mt-3">
                                <h6> Preview Capture:</h6>
                                <img id="previewImg" class="img-fluid border rounded" style="max-height: 200px;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.wajah.store', $karyawan->id ?? 1) }}" id="faceForm">
                @csrf
                <input type="hidden" name="face_encoding" id="face_encoding">
                <input type="hidden" name="face_image" id="face_image">

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="button" id="captureBtn" class="btn btn-primary" disabled>
                        <i class="fas fa-camera"></i> Capture Wajah
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-success" disabled>
                        <i class="fas fa-save"></i> Simpan ke Database
                    </button>
                    <button type="button" id="testBtn" class="btn btn-warning">
                        <i class="fas fa-vial"></i> Test Detection
                    </button>
                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.video-container {
    position: relative;
    overflow: hidden;
}

#video {
    transform: scaleX(-1);
    object-fit: cover;
}

#overlay {
    transform: scaleX(-1);
}

.gap-2 {
    gap: 0.5rem !important;
}
</style>

<!-- Load face-api.js -->
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>

<script>
// Global variables
const video = document.getElementById('video');
const overlay = document.getElementById('overlay');
const captureBtn = document.getElementById('captureBtn');
const submitBtn = document.getElementById('submitBtn');
const testBtn = document.getElementById('testBtn');
const detectionBadge = document.getElementById('detectionBadge');
const faceInfo = document.getElementById('faceInfo');
const status = document.getElementById('status');
const logContent = document.getElementById('logContent');

let detectionInterval = null;
let currentDetection = null;
let isCaptured = false;
let modelsLoaded = false;
let detectionFrameCount = 0;

// Debug logger
function log(message, type = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    const emoji = type === 'error' ? '' : type === 'success' ? '' : 'i';
    const msg = `[${timestamp}] ${emoji} ${message}`;
    console.log(msg);
    logContent.innerHTML += `<br>${msg}`;
    logContent.parentElement.scrollTop = logContent.parentElement.scrollHeight;
}

function showStatus(message, type) {
    status.className = `alert alert-${type}`;
    status.innerHTML = message;
    status.classList.remove('d-none');
}

// Test button - untuk cek apakah face-api.js loaded
testBtn.addEventListener('click', () => {
    log('=== RUNNING DIAGNOSTICS ===');
    log(`faceapi available: ${typeof faceapi !== 'undefined'}`);
    log(`Models loaded: ${modelsLoaded}`);
    log(`Video ready: ${video.readyState === 4}`);
    log(`Video dimensions: ${video.videoWidth}x${video.videoHeight}`);
    log(`Detection running: ${detectionInterval !== null}`);

    if (typeof faceapi === 'undefined') {
        showStatus(' face-api.js TIDAK LOADED! Cek koneksi internet.', 'danger');
    } else if (!modelsLoaded) {
        showStatus('⚠️ Models belum loaded. Tunggu sebentar...', 'warning');
    } else if (video.readyState !== 4) {
        showStatus('⚠️ Video belum ready. Tunggu kamera...', 'warning');
    } else {
        showStatus(' Semua sistem OK! Face detection seharusnya berjalan.', 'success');
    }
});

// Load models
async function loadModels() {
    log('Starting model loading...');
    showStatus('⏳ Loading AI models...', 'info');
    detectionBadge.innerHTML = '⏳ Loading...';

    try {
        // Check if faceapi exists
        if (typeof faceapi === 'undefined') {
            throw new Error('face-api.js library tidak loaded!');
        }

        log('face-api.js detected ✓', 'success');

        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';

        log('Loading TinyFaceDetector...');
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        log('✓ TinyFaceDetector loaded', 'success');

        log('Loading FaceLandmark68Net...');
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        log('✓ FaceLandmark68Net loaded', 'success');

        log('Loading FaceRecognitionNet...');
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        log('✓ FaceRecognitionNet loaded', 'success');

        modelsLoaded = true;
        detectionBadge.className = 'badge bg-success';
        detectionBadge.innerHTML = ' Models Ready';
        showStatus(' Models loaded! Starting camera...', 'success');

        setTimeout(startVideo, 500);

    } catch (error) {
        log(`ERROR loading models: ${error.message}`, 'error');
        showStatus(` Error: ${error.message}<br>Coba refresh page atau cek koneksi internet.`, 'danger');
        detectionBadge.className = 'badge bg-danger';
        detectionBadge.innerHTML = ' Error';
    }
}

// Start camera
async function startVideo() {
    log('Requesting camera access...');
    detectionBadge.innerHTML = '📹 Starting camera...';

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            }
        });

        video.srcObject = stream;
        log('✓ Camera started', 'success');

        video.onloadedmetadata = () => {
            log(`Video: ${video.videoWidth}x${video.videoHeight}`, 'success');
            detectionBadge.className = 'badge bg-info';
            detectionBadge.innerHTML = '📹 Camera Ready';
        };

    } catch (error) {
        log(`Camera ERROR: ${error.message}`, 'error');
        let msg = ' Camera error: ';
        if (error.name === 'NotAllowedError') msg += 'Permission denied';
        else if (error.name === 'NotFoundError') msg += 'No camera found';
        else msg += error.message;

        showStatus(msg, 'danger');
        detectionBadge.className = 'badge bg-danger';
        detectionBadge.innerHTML = ' Camera Error';
    }
}

// Start detection when video plays
video.addEventListener('play', () => {
    log('Video playing, setting up detection...');

    setTimeout(() => {
        if (!modelsLoaded) {
            log('Models not loaded yet!', 'error');
            showStatus(' Models belum loaded! Klik "Test Detection" untuk diagnose.', 'danger');
            return;
        }

        const displaySize = {
            width: video.videoWidth || 640,
            height: video.videoHeight || 480
        };

        overlay.width = displaySize.width;
        overlay.height = displaySize.height;
        faceapi.matchDimensions(overlay, displaySize);

        log(`Canvas: ${overlay.width}x${overlay.height}`, 'success');
        log('Starting detection loop...', 'success');

        detectionInterval = setInterval(detectFace, 100);
    }, 1000);
});

// Main detection function
async function detectFace() {
    if (isCaptured) return;

    try {
        const detection = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                inputSize: 320,
                scoreThreshold: 0.5
            }))
            .withFaceLandmarks()
            .withFaceDescriptor();

        const ctx = overlay.getContext('2d');
        ctx.clearRect(0, 0, overlay.width, overlay.height);

        if (detection) {
            detectionFrameCount++;

            if (detectionFrameCount === 1) {
                log('🎉 FIRST FACE DETECTED!', 'success');
                showStatus(' Wajah terdeteksi! Posisikan dengan baik lalu klik Capture.', 'success');
            }

            currentDetection = detection;

            const displaySize = { width: overlay.width, height: overlay.height };
            const resized = faceapi.resizeResults(detection, displaySize);
            const box = resized.detection.box;

            // Draw green box
            ctx.strokeStyle = '#00ff00';
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // Draw landmarks
            ctx.fillStyle = '#00ff00';
            resized.landmarks.positions.forEach(pt => {
                ctx.beginPath();
                ctx.arc(pt.x, pt.y, 2, 0, 2 * Math.PI);
                ctx.fill();
            });

            // Update UI
            detectionBadge.className = 'badge bg-success';
            detectionBadge.innerHTML = ' Face Detected';

            faceInfo.classList.remove('d-none');
            document.getElementById('confidence').textContent =
                (detection.detection.score * 100).toFixed(1) + '%';
            document.getElementById('faceSize').textContent =
                Math.round(box.width) + 'x' + Math.round(box.height);
            document.getElementById('detectionCount').textContent = detectionFrameCount;

            captureBtn.disabled = false;
        } else {
            currentDetection = null;
            detectionBadge.className = 'badge bg-warning';
            detectionBadge.innerHTML = '⚠️ No Face';
            faceInfo.classList.add('d-none');
            captureBtn.disabled = true;
        }
    } catch (error) {
        log(`Detection error: ${error.message}`, 'error');
    }
}

// Capture button
captureBtn.addEventListener('click', () => {
    if (!currentDetection) {
        showStatus(' Tidak ada wajah terdeteksi!', 'warning');
        return;
    }

    log('Capturing face...');
    captureBtn.disabled = true;

    try {
        // Get face descriptor
        const encoding = Array.from(currentDetection.descriptor);
        document.getElementById('face_encoding').value = JSON.stringify(encoding);
        log(`Descriptor captured: ${encoding.length} dimensions`, 'success');

        // Capture image
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);

        const imageData = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('face_image').value = imageData;
        log('Image captured', 'success');

        // Show preview
        document.getElementById('previewImg').src = imageData;
        document.getElementById('capturedPreview').classList.remove('d-none');

        isCaptured = true;
        clearInterval(detectionInterval);

        detectionBadge.className = 'badge bg-primary';
        detectionBadge.innerHTML = ' Captured';
        submitBtn.disabled = false;

        showStatus(' Wajah berhasil di-capture! Klik "Simpan ke Database".', 'success');
        log('Ready to submit!', 'success');
    } catch (error) {
        log(`Capture error: ${error.message}`, 'error');
        showStatus(' Error: ' + error.message, 'danger');
        captureBtn.disabled = false;
    }
});

// Form submit
document.getElementById('faceForm').addEventListener('submit', (e) => {
    if (!isCaptured) {
        e.preventDefault();
        showStatus(' Capture wajah dulu!', 'warning');
    } else {
        log('Submitting to backend...');
    }
});

// Initialize
window.addEventListener('load', () => {
    log('Page loaded');
    setTimeout(() => {
        if (typeof faceapi !== 'undefined') {
            loadModels();
        } else {
            log('face-api.js NOT LOADED! Retrying...', 'error');
            showStatus(' face-api.js gagal load. Cek koneksi internet dan refresh.', 'danger');
        }
    }, 500);
});
</script>

</body>
</html>
