@extends('karyawan.layout.fullscreen')

@section('title', 'Ajukan Libur Pengganti')

@push('styles')
{{-- Gunakan style yang sama dengan create cuti, sesuaikan warna --}}
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    }

    body {
        margin: 0;
        padding: 0;
        background: #ffffff;
        overflow: hidden;
    }

    .fullscreen-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .pengajuan-content {
        margin-top: 60px;
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background: #f8f9fa;
        padding-bottom: 100px;
    }

    .form-section {
        background: white;
        padding: 25px 20px;
        margin: 0;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #FF6B35;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .form-label .required {
        color: #dc3545;
        margin-left: 3px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s;
        background: #f7fafc;
        color: #2d3748;
    }

    .form-control:focus {
        outline: none;
        border-color: #FF6B35;
        background: white;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    select.form-control {
        cursor: pointer;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 130px;
        font-family: inherit;
        line-height: 1.6;
    }

    .date-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .duration-info {
        background: linear-gradient(135deg, #e8f4fd 0%, #d3eafd 100%);
        border-left: 4px solid #FF6B35;
        padding: 16px 20px;
        border-radius: 10px;
        font-size: 14px;
        color: #2d3748;
        margin-top: 15px;
        display: none;
        animation: slideDown 0.3s ease-out;
    }

    .duration-info.show {
        display: flex;
        align-items: center;
        gap: 12px;
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

    .duration-info i {
        color: #FF6B35;
        font-size: 20px;
    }

    .duration-info strong {
        color: #1a202c;
        font-weight: 700;
        font-size: 15px;
    }

    .character-count {
        font-size: 12px;
        color: #718096;
        margin-top: 5px;
        text-align: right;
    }

    .character-count.warning {
        color: #f59e0b;
    }

    .character-count.error {
        color: #dc3545;
    }

    .form-footer {
        position: fixed;
        /* bottom: 70px; */
        left: 0;
        right: 0;
        padding: 15px 20px;
        background: white;
        border-top: 2px solid #e2e8f0;
        z-index: 100;
        box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.08);
    }

    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
    }

    .btn-submit:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 10px rgba(255, 107, 53, 0.3);
    }

    .btn-submit:disabled {
        background: linear-gradient(135deg, #cbd5e0 0%, #a0aec0 100%);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-submit i {
        font-size: 18px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-section {
            padding: 20px 15px;
        }

        .date-row {
            grid-template-columns: 1fr;
        }

        .form-footer {
            padding: 12px 15px;
            /* bottom: 60px; */
        }
    }

    @media (max-width: 480px) {
        .section-title {
            font-size: 16px;
        }

        .btn-submit {
            padding: 14px 25px;
            font-size: 15px;
        }

        .form-control {
            font-size: 16px;
        }

        .form-footer {
            bottom: 1px;
        }
    }
    .btn-submit { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .section-title i { color: #11998e; }
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">
    <div class="pengajuan-content">
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-umbrella-beach"></i>
                <span>Form Pengajuan Libur Pengganti</span>
            </div>

            <form method="POST" action="{{ route('karyawan.libur-pengganti.store') }}" id="formLibur" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Tanggal Libur <span class="required">*</span></label>
                    <input type="date" name="tanggal" class="form-control" required min="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Alasan <span class="required">*</span></label>
                    <textarea name="alasan" class="form-control" placeholder="Jelaskan alasan pengambilan libur pengganti..." required maxlength="500"></textarea>
                    <div class="character-count">0 / 500 karakter</div>
                </div>

                <div class="form-group">
                    <label class="form-label">File Pendukung (Opsional)</label>
                    <input type="file" name="file_pendukung" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <small>Format: PDF, JPG, PNG. Maksimal 2MB</small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Saldo Anda saat ini: <strong>{{ $saldo->saldo ?? 0 }} hari</strong>. Setiap pengajuan yang disetujui akan mengurangi saldo 1 hari.
                </div>
            </form>
        </div>
    </div>

    <div class="form-footer">
        <button type="submit" form="formLibur" class="btn-submit">
            <i class="fas fa-paper-plane"></i> Kirim Pengajuan
        </button>
    </div>
</div>
@endsection