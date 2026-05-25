{{-- resources/views/karyawan/wajah/capture.blade.php --}}
@extends('karyawan.layout.fullscreen')

@section('title', 'Capture Wajah Baru')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
<style>
    body { margin:0; padding:0; background:#1a1a1a; overflow:hidden; }

    .fullscreen-container {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        display: flex;
        flex-direction: column;
        background: #1a1a1a;
    }

    .header-bar {
        background: #1d4ed8;
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        flex-shrink: 0;
    }

    .back-btn {
        background: none; border: none; color: white;
        font-size: 22px; cursor: pointer; padding: 0;
    }

    .header-title {
        flex: 1; text-align: center;
        font-size: 18px; font-weight: 700;
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
        width: 100%; height: 100%;
        object-fit: cover;
        transform: scaleX(-1);
    }

    .face-guide {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 260px; height: 340px;
        border: 3px dashed rgba(255,255,255,0.8);
        border-radius: 50%;
        pointer-events: none;
        z-index: 3;
        transition: border-color 0.3s;
    }

    .face-guide.detected { border-color: #10b981; border-style: solid; }

    .video-overlay {
        position: absolute; top:0; left:0; right:0; bottom:0;
        pointer-events: none; z-index: 2;
    }

    .video-overlay::before {
        content: '';
        position: absolute; top:0; left:0; right:0; bottom:0;
        background: radial-gradient(ellipse 260px 340px at center, transparent 0%, transparent 50%, rgba(0,0,0,0.7) 50%);
    }

    .bottom-panel {
        background: white;
        padding: 18px 20px 24px;
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    .info-banner {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 12px;
        color: #1d4ed8;
        margin-bottom: 14px;
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .status-grid { display: grid; gap: 8px; margin-bottom: 14px; }

    .status-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .status-label { font-size: 13px; color: #666; font-weight: 500; }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger  { background: #f8d7da; color: #721c24; }
    .badge-warning { background: #fff3cd; color: #856404; }

    .capture-steps {
        display: flex;
        gap: 6px;
        margin-bottom: 14px;
    }

    .step-dot {
        flex: 1; height: 4px;
        border-radius: 2px;
        background: #e2e8f0;
        transition: background 0.3s;
    }

    .step-dot.active { background: #1d4ed8; }
    .step-dot.done   { background: #10b981; }

    .submit-btn {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        background: #1d4ed8;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .submit-btn:disabled { background: #cbd5e1; cursor: not-allowed; }
    .submit-btn:not(:disabled):hover { background: #1e40af; }
</style>
@endpush

@section('content')
<div class="fullscreen-container">

    <div class="header-bar">
        <button class="back-btn" onclick="window.location.href='{{ route('settings.index') }}'">
            <i class="fas fa-arrow-left"></i>
        </button>
        <div class="header-title">
            <i class="fas fa-camera"></i> Capture Wajah Baru
        </div>
        <div style="width:22px;"></div>
    </div>

    <div class="video-wrapper">
        <video id="video" autoplay muted playsinline></video>
        <div class="video-overlay"></div>
        <div class="face-guide" id="faceGuide"></div>
    </div>

    <div class="bottom-panel">

        <div class="info-banner">
            <i class="fas fa-info-circle" style="margin-top:1px;flex-shrink:0;"></i>
            <span>Posisikan wajah di dalam lingkaran. Pastikan pencahayaan cukup dan wajah terlihat jelas.</span>
        </div>

        {{-- Progress dots --}}
        <div class="capture-steps">
            <div class="step-dot active" id="step1"></div>
            <div class="step-dot" id="step2"></div>
            <div class="step-dot" id="step3"></div>
        </div>

        <div class="status-grid">
            <div class="status-row">
                <span class="status-label">Status Wajah</span>
                <span class="badge badge-warning" id="detectionBadge">Mendeteksi...</span>
            </div>
            <div class="status-row">
                <span class="status-label">Kualitas</span>
                <span id="qualityScore" style="font-size:13px;font-weight:600;color:#666;">-</span>
            </div>
        </div>

        <form id="captureForm" action="{{ route('karyawan.wajah.capture-store') }}" method="POST">
            @csrf
            <input type="hidden" name="face_encoding" id="faceEncoding">
            <input type="hidden" name="face_image"    id="faceImage">

            <button type="submit" id="submitBtn" class="submit-btn" disabled>
                <i class="fas fa-save"></i> Simpan Template Wajah
            </button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
let videoStream    = null;
let detectionLoop  = null;
let modelsLoaded   = false;
let currentDetect  = null;
let captureCount   = 0; // jumlah frame cocok berturut-turut

const REQUIRED_STABLE = 8; // harus stabil 8 frame berturut-turut

(async () => {
    await loadModels();
    await startCamera();
})();

async function loadModels() {
    const URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(URL),
        faceapi.nets.faceLandmark68Net.loadFromUri(URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(URL),
    ]);
    modelsLoaded = true;
}

async function startCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: 640, height: 480 }
    });
    videoStream = stream;
    const video = document.getElementById('video');
    video.srcObject = stream;
    video.onloadedmetadata = () => startDetection();
}

async function startDetection() {
    const video    = document.getElementById('video');
    const badge    = document.getElementById('detectionBadge');
    const quality  = document.getElementById('qualityScore');
    const btn      = document.getElementById('submitBtn');
    const guide    = document.getElementById('faceGuide');

    detectionLoop = setInterval(async () => {
        const det = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!det) {
            captureCount = 0;
            badge.className = 'badge badge-warning';
            badge.innerHTML = '<i class="fas fa-exclamation"></i> Tidak Terdeteksi';
            quality.textContent = '-';
            btn.disabled = true;
            guide.className = 'face-guide';
            updateSteps(0);
            return;
        }

        const score = (det.detection.score * 100).toFixed(1);
        quality.textContent = score + '%';

        if (det.detection.score >= 0.85) {
            captureCount = Math.min(captureCount + 1, REQUIRED_STABLE);
        } else {
            captureCount = Math.max(captureCount - 1, 0);
        }

        const progress = Math.round((captureCount / REQUIRED_STABLE) * 3);
        updateSteps(progress);

        if (captureCount >= REQUIRED_STABLE) {
            badge.className = 'badge badge-success';
            badge.innerHTML = '<i class="fas fa-check"></i> Siap Disimpan';
            guide.className = 'face-guide detected';
            btn.disabled = false;
            currentDetect = det;
        } else {
            badge.className = 'badge badge-warning';
            badge.innerHTML = '<i class="fas fa-sync fa-spin"></i> Menganalisis...';
            guide.className = 'face-guide';
            btn.disabled = true;
        }
    }, 150);
}

function updateSteps(level) {
    for (let i = 1; i <= 3; i++) {
        const el = document.getElementById('step' + i);
        if (i < level)       { el.className = 'step-dot done'; }
        else if (i === level) { el.className = 'step-dot active'; }
        else                  { el.className = 'step-dot'; }
    }
}

document.getElementById('captureForm').addEventListener('submit', function(e) {
    if (!currentDetect) {
        e.preventDefault();
        alert('Wajah belum terdeteksi dengan stabil. Tunggu hingga tombol aktif.');
        return;
    }

    const video  = document.getElementById('video');
    const canvas = document.createElement('canvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0);

    document.getElementById('faceEncoding').value = JSON.stringify(Array.from(currentDetect.descriptor));
    document.getElementById('faceImage').value    = canvas.toDataURL('image/jpeg', 0.9);
});

window.addEventListener('beforeunload', () => {
    if (videoStream)   videoStream.getTracks().forEach(t => t.stop());
    if (detectionLoop) clearInterval(detectionLoop);
});
</script>
@endpush
