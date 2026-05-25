@extends('karyawan.layout.fullscreen')

@section('title', 'Daftar Wajah')

@push('styles')
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
            background: linear-gradient(135deg, #4285f4 0%, #5a98f7 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            flex-shrink: 0;
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

        .detection-box {
            position: absolute;
            border: 3px solid #00ff00;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.5);
            z-index: 4;
            pointer-events: none;
            display: none;
        }

        .bottom-panel {
            background: white;
            padding: 10px;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .instruction-box {
            background: linear-gradient(135deg, #e8f4fd 0%, #d3eafd 100%);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #4285f4;
        }

        .instruction-title {
            font-size: 14px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .instruction-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .instruction-list li {
            font-size: 13px;
            color: #4a5568;
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .instruction-list li i {
            color: #4285f4;
            font-size: 12px;
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

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-primary {
            background: #cce5ff;
            color: #004085;
        }

        .action-buttons {
            display: grid;
            /* grid-template-columns: 1fr 1fr; */
    grid-template-columns: repeat(2, 1fr);

            gap: 10px;
        }

        .btn {
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-capture {
            background: linear-gradient(135deg, #4285f4 0%, #5a98f7 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);
        }

        .btn-capture:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    /* grid-column: span 2; ⬅️ kunci */
        }

        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn:active:not(:disabled) {
            transform: scale(0.98);
        }

        .preview-box {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            display: none;
        }

        .preview-box.show {
            display: block;
        }

        .preview-title {
            font-size: 13px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .preview-img {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
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
                <i class="fas fa-user-circle"></i> Daftar Wajah
            </div>
            <div style="width: 24px;"></div>
        </div>

        <!-- Video Area -->
        <div class="video-wrapper">
            <video id="video" autoplay muted playsinline></video>
            <div class="video-overlay"></div>
            <div class="face-guide"></div>
            {{-- <div id="detectionBox" class="detection-box"></div> --}}
        </div>

        <!-- Bottom Panel -->
        <div class="bottom-panel">
            <!-- Instructions -->
            {{-- <div class="instruction-box">
                <div class="instruction-title">
                    <i class="fas fa-info-circle"></i>
                    Petunjuk Pendaftaran Wajah
                </div>
                <ul class="instruction-list">
                    <li><i class="fas fa-check-circle"></i> Pastikan pencahayaan cukup terang</li>
                    <li><i class="fas fa-check-circle"></i> Posisikan wajah di dalam lingkaran</li>
                    <li><i class="fas fa-check-circle"></i> Jarak ideal 50-80cm dari kamera</li>
                    <li><i class="fas fa-check-circle"></i> Tatap kamera dengan wajah lurus</li>
                </ul>
            </div> --}}

            <!-- Status -->
            <div class="status-grid">
                <div class="status-row">
                    <span class="status-label">Status Kamera</span>
                    <span class="badge badge-warning" id="cameraBadge">
                        <i class="fas fa-clock"></i> Loading...
                    </span>
                </div>
                <div class="status-row">
                    <span class="status-label">Status Wajah</span>
                    <span class="badge badge-warning" id="detectionBadge">
                        <i class="fas fa-clock"></i> Menunggu...
                    </span>
                </div>
                <div class="status-row">
                    <span class="status-label">Kualitas Deteksi</span>
                    <span id="confidence">-</span>
                </div>
            </div>

            <!-- Preview -->
            <div class="preview-box" id="previewBox">
                <div class="preview-title">
                    <i class="fas fa-image"></i> Preview Wajah Tertangkap
                </div>
                <img id="previewImg" class="preview-img" alt="Preview">
            </div>

            <!-- Form -->
            <form id="faceForm" action="{{ route('karyawan.wajah.store') }}" method="POST">
                @csrf
                <input type="hidden" name="face_encoding" id="faceEncoding">
                <input type="hidden" name="face_image" id="faceImage">
                <input type="hidden" name="face_confidence" id="faceConfidence">

                <div class="action-buttons">
                    <button type="button" id="captureBtn" class="btn btn-capture" disabled>
                        <i class="fas fa-camera"></i> Capture
                    </button>
                    <button type="button" id="retakeBtn" class="btn btn-capture" style="display: none;">
                        <i class="fas fa-redo"></i> Ulangi
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-submit" disabled>
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Global variables
const video = document.getElementById('video');
const detectionBox = document.getElementById('detectionBox');
const cameraBadge = document.getElementById('cameraBadge');
const detectionBadge = document.getElementById('detectionBadge');
const captureBtn = document.getElementById('captureBtn');
const retakeBtn = document.getElementById('retakeBtn');
const submitBtn = document.getElementById('submitBtn');
const previewBox = document.getElementById('previewBox');
const previewImg = document.getElementById('previewImg');
const confidenceEl = document.getElementById('confidence');

let videoStream = null;
let detectionInterval = null;
let modelsLoaded = false;
let currentDetection = null;
let isCaptured = false;

// Auto start
(async () => {
    await loadModels();
    await startCamera();
})();

async function loadModels() {
    try {
        cameraBadge.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading AI...';

        const URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(URL)
        ]);

        modelsLoaded = true;
        cameraBadge.className = 'badge badge-success';
        cameraBadge.innerHTML = '<i class="fas fa-check-circle"></i> AI Ready';
    } catch (error) {
        cameraBadge.className = 'badge badge-danger';
        cameraBadge.innerHTML = '<i class="fas fa-times-circle"></i> Error';
        alert('Gagal memuat AI models. Coba refresh halaman.');
    }
}

async function startCamera() {
    try {
        cameraBadge.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';

        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'user',
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        });

        videoStream = stream;
        video.srcObject = stream;

        video.onloadedmetadata = () => {
            cameraBadge.className = 'badge badge-success';
            cameraBadge.innerHTML = '<i class="fas fa-video"></i> Camera Active';
            startDetection();
        };
    } catch (error) {
        cameraBadge.className = 'badge badge-danger';
        cameraBadge.innerHTML = '<i class="fas fa-times-circle"></i> Camera Error';
        alert('Gagal mengakses kamera! Pastikan izin kamera sudah diberikan.');
    }
}

async function startDetection() {
    if (!modelsLoaded) return;

    detectionInterval = setInterval(async () => {
        if (isCaptured) return;

        try {
            const detection = await faceapi
                .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                    inputSize: 320,
                    scoreThreshold: 0.5
                }))
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (detection) {
                currentDetection = detection;

                // Update badge
                detectionBadge.className = 'badge badge-success';
                detectionBadge.innerHTML = '<i class="fas fa-check-circle"></i> Terdeteksi';

                // Update confidence
                const conf = (detection.detection.score * 100).toFixed(1);
                confidenceEl.innerHTML = `<strong style="color: #28a745;">${conf}%</strong>`;

                // Enable capture button
                captureBtn.disabled = false;

                // Draw detection box
                const box = detection.detection.box;
                const videoRect = video.getBoundingClientRect();
                const scaleX = videoRect.width / video.videoWidth;
                const scaleY = videoRect.height / video.videoHeight;

                detectionBox.style.display = 'block';
                detectionBox.style.left = (box.x * scaleX) + 'px';
                detectionBox.style.top = (box.y * scaleY) + 'px';
                detectionBox.style.width = (box.width * scaleX) + 'px';
                detectionBox.style.height = (box.height * scaleY) + 'px';
            } else {
                currentDetection = null;

                detectionBadge.className = 'badge badge-warning';
                detectionBadge.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Tidak Terdeteksi';

                confidenceEl.textContent = '-';
                captureBtn.disabled = true;
                detectionBox.style.display = 'none';
            }
        } catch (error) {
            console.error('Detection error:', error);
        }
    }, 100);
}

// Capture button
captureBtn.addEventListener('click', () => {
    if (!currentDetection) {
        alert('Wajah belum terdeteksi!');
        return;
    }

    // Stop detection
    isCaptured = true;
    clearInterval(detectionInterval);

    // Get face descriptor
    const encoding = Array.from(currentDetection.descriptor);
    document.getElementById('faceEncoding').value = JSON.stringify(encoding);
    document.getElementById('faceConfidence').value = (currentDetection.detection.score * 100).toFixed(2);

    // Capture image
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');

    // Mirror image
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('faceImage').value = imageData;

    // Show preview
    previewImg.src = imageData;
    previewBox.classList.add('show');

    // Update UI
    detectionBadge.className = 'badge badge-primary';
    detectionBadge.innerHTML = '<i class="fas fa-camera"></i> Captured';

    captureBtn.style.display = 'none';
    retakeBtn.style.display = 'flex';
    submitBtn.disabled = false;

    // Stop camera
    if (videoStream) {
        videoStream.getTracks().forEach(t => t.stop());
    }
});

// Retake button
retakeBtn.addEventListener('click', async () => {
    isCaptured = false;

    // Reset UI
    previewBox.classList.remove('show');
    captureBtn.style.display = 'flex';
    retakeBtn.style.display = 'none';
    submitBtn.disabled = true;

    detectionBadge.className = 'badge badge-warning';
    detectionBadge.innerHTML = '<i class="fas fa-clock"></i> Menunggu...';

    confidenceEl.textContent = '-';

    // Clear form
    document.getElementById('faceEncoding').value = '';
    document.getElementById('faceImage').value = '';

    // Restart camera
    await startCamera();
});

// Form submit
document.getElementById('faceForm').addEventListener('submit', (e) => {
    if (!isCaptured) {
        e.preventDefault();
        alert('Capture wajah terlebih dahulu!');
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
});

// Cleanup
window.addEventListener('beforeunload', () => {
    if (videoStream) {
        videoStream.getTracks().forEach(t => t.stop());
    }
    if (detectionInterval) {
        clearInterval(detectionInterval);
    }
});
</script>
@endpush
