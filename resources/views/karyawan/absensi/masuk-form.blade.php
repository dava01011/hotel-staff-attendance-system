{{-- resources/views/karyawan/absensi/masuk-form.blade.php - AUTO SUBMIT + OPTIMASI --}}
@extends('karyawan.layout.fullscreen')

@section('title', 'Absen Masuk')

@push('styles')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #1a1a1a;
            overflow: hidden;
        }

        .fullscreen-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            background: #1a1a1a;
        }

        .header-bar {
            background: #28a745;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .back-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
        }

        .header-title {
            flex: 1;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
        }

        .video-wrapper {
            flex: 1;
            position: relative;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        #video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .face-guide {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 280px;
            height: 360px;
            border: 3px dashed rgba(255,255,255,0.8);
            border-radius: 50%;
            pointer-events: none;
            z-index: 3;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 2;
        }

        .video-overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(
                ellipse 280px 360px at center,
                transparent 0%,
                transparent 50%,
                rgba(0, 0, 0, 0.7) 50%
            );
        }

        .bottom-panel {
            background: white;
            padding: 20px;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.1);
        }

        .status-grid {
            display: grid;
            gap: 10px;
            margin-bottom: 15px;
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .status-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            background: #28a745;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .submit-btn.auto-submit {
            animation: pulse 1s infinite;
            background: #20c997;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .helper-text {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
        }

        .location-info {
            background: #e7f3ff;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            color: #0c5aa0;
            margin-bottom: 15px;
            display: none;
        }

        .location-info.show {
            display: block;
        }

        .auto-submit-message {
            text-align: center;
            padding: 10px;
            background: #20c997;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 10px;
            display: none;
            animation: slideDown 0.3s ease;
        }

        .auto-submit-message.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
@endpush

@section('content')
    <div class="fullscreen-container">
        <!-- Header -->
        <div class="header-bar">
            <button class="back-btn" onclick="window.location.href='{{ route('karyawan.dashboard') }}'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="header-title">
                <i class="fas fa-sign-in-alt"></i> Absen Masuk
            </div>
            <div style="width: 24px;"></div>
        </div>

        <!-- Video Area -->
        <div class="video-wrapper">
            <video id="video" autoplay muted playsinline></video>
            <div class="video-overlay"></div>
            <div class="face-guide"></div>
        </div>

        <!-- Bottom Panel -->
        <div class="bottom-panel">
            <p class="helper-text">
                Posisikan wajah di dalam lingkaran dan pastikan pencahayaan cukup
            </p>

            {{-- Auto submit message --}}
            <div class="auto-submit-message" id="autoSubmitMsg">
                <i class="fas fa-check-circle"></i> Wajah terdeteksi! Submitting...
            </div>

            <div class="location-info" id="locationInfo">
                <i class="fas fa-map-marker-alt"></i>
                <strong id="locationName">{{ $lokasiKantor->nama_lokasi }}</strong><br>
                Radius: <span id="locationRadius">{{ $lokasiKantor->radius }}</span>m
            </div>

            <div class="status-grid">
                <div class="status-row">
                    <span class="status-label">Status Wajah</span>
                    <span class="badge badge-warning" id="detectionBadge">Menunggu...</span>
                </div>
                <div class="status-row">
                    <span class="status-label">Kecocokan</span>
                    <span id="matchScore">-</span>
                </div>
                <div class="status-row">
                    <span class="status-label">Status Lokasi</span>
                    <span class="badge badge-warning" id="locationBadge">Mengecek...</span>
                </div>
            </div>

            <form id="absenForm" action="{{ route('karyawan.absensi.masuk') }}" method="POST">
                @csrf
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lon">
                <input type="hidden" name="face_image" id="faceImage">
                <input type="hidden" name="face_encoding" id="faceEncoding">
                <input type="hidden" name="face_confidence" id="faceConfidence">

                <button type="submit" id="submitBtn" class="submit-btn" disabled>
                    <i class="fas fa-check"></i> Submit Absen Masuk
                </button>
            </form>
        </div>

        <audio id="beepSuccess" src="/sounds/beep-success.wav" preload="auto"></audio>
    </div>
@endsection

@push('scripts')
<script>
let lastBeep = 0;
let autoSubmitReady = false;
let isSubmitting = false;
let locationCheckCached = false;
let cachedLocationValid = false;

// ===== AMBIL DATA LOKASI DARI DATABASE VIA BLADE =====
// OPTIMASI: Cache di memory, tidak perlu kalkulasi berulang
const OFFICE_LAT = {{ $lokasiKantor->latitude }};
const OFFICE_LNG = {{ $lokasiKantor->longitude }};
const OFFICE_RADIUS = {{ $lokasiKantor->radius }};
const OFFICE_RADIUS_SQ = OFFICE_RADIUS * OFFICE_RADIUS;  // Pre-compute untuk squared comparison
const OFFICE_NAME = "{{ $lokasiKantor->nama_lokasi }}";

const registeredFace = {!! json_encode($wajahKaryawan ? json_decode($wajahKaryawan->face_encoding) : null) !!};

// Global variables
let videoStream = null;
let detectionInterval = null;
let modelsLoaded = false;
let currentDetection = null;
let locationValid = false;
let userLat, userLng;
let consecutiveMatches = 0;  // Track berapa kali cocok berturut-turut

const MATCH_THRESHOLD_CONSECUTIVE = 3;  // Harus cocok 3x berturut-turut baru auto-submit

// Check face registration
if (!registeredFace) {
    alert('Wajah belum terdaftar! Hubungi admin untuk registrasi.');
    window.location.href = '{{ route("karyawan.dashboard") }}';
}

// ===== OPTIMASI LOKASI: GET LOCATION ONCE AT START =====
// Cache hasil geolocation, jangan berubah selama session
function getLocationOnce() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;

                document.getElementById('lat').value = userLat;
                document.getElementById('lon').value = userLng;

                // OPTIMASI: Hitung distance SEKALI saja saat awal
                const distance = calculateDistanceOptimized(userLat, userLng, OFFICE_LAT, OFFICE_LNG);
                locationCheckCached = true;
                cachedLocationValid = distance <= OFFICE_RADIUS;

                const badge = document.getElementById('locationBadge');
                if (cachedLocationValid) {
                    locationValid = true;
                    badge.className = 'badge badge-success';
                    badge.innerHTML = '<i class="fas fa-check-circle"></i> Dalam Radius (' + distance.toFixed(0) + 'm)';
                    document.getElementById('locationInfo').classList.add('show');
                } else {
                    locationValid = false;
                    badge.className = 'badge badge-danger';
                    badge.innerHTML = '<i class="fas fa-times-circle"></i> ' + distance.toFixed(0) + 'm (Radius: ' + OFFICE_RADIUS + 'm)';
                    document.getElementById('locationInfo').classList.add('show');
                }
            },
            () => {
                alert('Gagal mendapatkan lokasi. Aktifkan GPS!');
                document.getElementById('locationBadge').className = 'badge badge-danger';
                document.getElementById('locationBadge').innerHTML = '<i class="fas fa-times-circle"></i> GPS Error';
            }
        );
    } else {
        alert('Geolocation tidak didukung oleh browser Anda!');
    }
}

// ===== OPTIMASI DISTANCE CALCULATION =====
// Gunakan approximation untuk kecepatan, akurat untuk range < 10km
function calculateDistanceOptimized(lat1, lon1, lat2, lon2) {
    // Haversine formula yang sudah dioptimasi
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Initialize
getLocationOnce();

// Auto start
(async () => {
    await loadModels();
    await startCamera();
})();

async function loadModels() {
    if (modelsLoaded) return;
    try {
        const URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(URL)
        ]);
        modelsLoaded = true;
    } catch (e) {
        alert('Gagal memuat face recognition');
    }
}

async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: 640, height: 480 }
        });
        videoStream = stream;
        const video = document.getElementById('video');
        video.srcObject = stream;

        video.onloadedmetadata = () => {
            startDetection();
        };
    } catch (error) {
        alert('Gagal akses kamera! Pastikan izin kamera sudah diberikan.');
    }
}

async function startDetection() {
    const video = document.getElementById('video');
    const badge = document.getElementById('detectionBadge');
    const btn = document.getElementById('submitBtn');
    const scoreEl = document.getElementById('matchScore');

    detectionInterval = setInterval(async () => {
        const detection = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!detection) {
            badge.className = 'badge badge-warning';
            badge.innerHTML = '<i class="fas fa-exclamation"></i> Tidak Terdeteksi';
            btn.disabled = true;
            scoreEl.textContent = '-';
            autoSubmitReady = false;
            consecutiveMatches = 0;
            document.getElementById('autoSubmitMsg').classList.remove('show');
            return;
        }

        // Hitung kecocokan
        const dist = euclideanDistance(Array.from(detection.descriptor), registeredFace);
        const match = dist < 0.4;
        const confidence = ((1 - dist) * 100).toFixed(1);

        // Track consecutive matches untuk auto-submit
        if (match && locationValid) {
            consecutiveMatches++;
        } else {
            consecutiveMatches = 0;
        }

        // Update status
        const now = Date.now();
        if (match) {
            badge.className = 'badge badge-success';
            badge.innerHTML = '<i class="fas fa-check"></i> Cocok';
            btn.disabled = !locationValid;

            if (now - lastBeep > 2000) {
                document.getElementById('beepSuccess').play();
                lastBeep = now;
            }

            // ===== AUTO SUBMIT LOGIC =====
            // Jika cocok 3x berturut-turut dan lokasi valid, auto-submit
            if (consecutiveMatches >= MATCH_THRESHOLD_CONSECUTIVE && locationValid && !isSubmitting) {
                autoSubmitReady = true;
                document.getElementById('autoSubmitMsg').classList.add('show');

                // Delay 500ms untuk user experience yang smooth
                setTimeout(() => {
                    if (autoSubmitReady && !isSubmitting) {
                        autoSubmit();
                    }
                }, 500);
            }
        } else {
            badge.className = 'badge badge-danger';
            badge.innerHTML = '<i class="fas fa-times"></i> Tidak Cocok';
            btn.disabled = true;
            autoSubmitReady = false;
            consecutiveMatches = 0;
            document.getElementById('autoSubmitMsg').classList.remove('show');
        }

        scoreEl.textContent = confidence + '%';
        currentDetection = detection;
    }, 150);
}

function euclideanDistance(a, b) {
    if (!a || !b || a.length !== b.length) return 999;
    let sum = 0;
    for (let i = 0; i < a.length; i++) {
        sum += Math.pow(a[i] - b[i], 2);
    }
    return Math.sqrt(sum);
}

// ===== AUTO SUBMIT FUNCTION =====
function autoSubmit() {
    if (!currentDetection || !locationValid || isSubmitting) {
        return;
    }

    isSubmitting = true;
    const btn = document.getElementById('submitBtn');
    btn.classList.add('auto-submit');
    btn.disabled = true;

    const video = document.getElementById('video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0);

    document.getElementById('faceImage').value = canvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('faceEncoding').value = JSON.stringify(Array.from(currentDetection.descriptor));
    document.getElementById('faceConfidence').value = (currentDetection.detection.score * 100).toFixed(2);

    // Auto submit form
    document.getElementById('absenForm').submit();
}

// Manual submit (jika user klik tombol)
document.getElementById('absenForm').addEventListener('submit', (e) => {
    if (!currentDetection) {
        e.preventDefault();
        alert('Wajah belum terdeteksi!');
        return;
    }
    if (!locationValid) {
        e.preventDefault();
        alert('Lokasi Anda di luar radius ' + OFFICE_NAME + '!');
        return;
    }

    const video = document.getElementById('video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0);

    document.getElementById('faceImage').value = canvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('faceEncoding').value = JSON.stringify(Array.from(currentDetection.descriptor));
    document.getElementById('faceConfidence').value = (currentDetection.detection.score * 100).toFixed(2);
});

// Cleanup
window.addEventListener('beforeunload', () => {
    if (videoStream) videoStream.getTracks().forEach(t => t.stop());
    if (detectionInterval) clearInterval(detectionInterval);
});
</script>
@endpush
