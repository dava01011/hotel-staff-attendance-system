@extends('karyawan.layout.fullscreen')

@section('title', 'Ajukan Shift')

@push('styles')
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
        color: #354591;
    }

    .current-shift-info {
        background: linear-gradient(135deg, #e8f4fd 0%, #d3eafd 100%);
        border-left: 4px solid #354591;
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
    }

    .info-title {
        font-size: 13px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 11px;
        color: #718096;
        margin-bottom: 3px;
        font-weight: 600;
    }

    .info-value {
        font-size: 14px;
        color: #2d3748;
        font-weight: 700;
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
        border-color: #354591;
        background: white;
        box-shadow: 0 0 0 3px rgba(53, 69, 145, 0.1);
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

    .radio-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 8px;
    }

    .radio-option {
        position: relative;
    }

    .radio-option input[type="radio"] {
        display: none;
    }

    .radio-label {
        display: block;
        padding: 16px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        background: #f7fafc;
    }

    .radio-label i {
        display: block;
        font-size: 24px;
        margin-bottom: 8px;
        color: #718096;
    }

    .radio-option input[type="radio"]:checked + .radio-label {
        border-color: #354591;
        background: linear-gradient(135deg, #e8f4fd 0%, #d3eafd 100%);
        color: #354591;
    }

    .radio-option input[type="radio"]:checked + .radio-label i {
        color: #354591;
    }

    .date-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    #tanggal_selesai_group {
        transition: all 0.3s;
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
        bottom: 1px;
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
        background: linear-gradient(135deg, #354591 0%, #4a5db8 100%);
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
        box-shadow: 0 4px 15px rgba(53, 69, 145, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(53, 69, 145, 0.4);
    }

    .btn-submit:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 10px rgba(53, 69, 145, 0.3);
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

        .radio-label {
            padding: 14px 10px;
        }

        .radio-label i {
            font-size: 20px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">
    {{-- Scrollable Content --}}
    <div class="pengajuan-content">
        {{-- Form Pengajuan Shift --}}
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-sync-alt"></i>
                <span>Form Pengajuan Shift</span>
            </div>

            {{-- Current Shift Info --}}
            <div class="current-shift-info">
                <div class="info-title">
                    <i class="fas fa-info-circle"></i>
                    Shift Aktif Saat Ini
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Kode Shift</div>
                        <div class="info-value">{{ $jadwalShiftAktif->shift->kode }}</div>
                    </div>
                    {{-- <div class="info-item">
                        <div class="info-label">Jenis Shift</div>
                        <div class="info-value">{{ $jadwalShiftAktif->shift->jenis }}</div>
                    </div> --}}
                    <div class="info-item">
                        <div class="info-label">Jam Masuk</div>
                        <div class="info-value">{{ substr($jadwalShiftAktif->shift->jam_masuk, 0, 5) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Jam Pulang</div>
                        <div class="info-value">{{ substr($jadwalShiftAktif->shift->jam_pulang, 0, 5) }}</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('karyawan.ajukan-shift.store') }}" id="formShift">
                @csrf

                <div class="form-group">
                    <label class="form-label">
                        Jenis Pengajuan
                        <span class="required">*</span>
                    </label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" name="jenis" value="sementara" id="jenis_sementara" required onchange="toggleTanggalSelesai()">
                            <label for="jenis_sementara" class="radio-label">
                                <i class="fas fa-calendar-alt"></i>
                                Sementara
                            </label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" name="jenis" value="permanen" id="jenis_permanen" required onchange="toggleTanggalSelesai()">
                            <label for="jenis_permanen" class="radio-label">
                                <i class="fas fa-infinity"></i>
                                Permanen
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Shift Pengganti
                        <span class="required">*</span>
                    </label>
                    <select name="shift_baru_id" id="shift_baru" class="form-control" required>
                        <option value="">-- Pilih Shift Pengganti --</option>
                        @foreach($allShifts as $shift)
                            <option value="{{ $shift->id }}">
                                {{ $shift->kode }} - {{ $shift->jenis }}
                                ({{ substr($shift->jam_masuk, 0, 5) }} - {{ substr($shift->jam_pulang, 0, 5) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="date-row">
                    <div class="form-group">
                        <label class="form-label">
                            Tanggal Mulai
                            <span class="required">*</span>
                        </label>
                        <input type="date"
                               id="shift_tanggal_mulai"
                               name="tanggal_mulai"
                               class="form-control"
                               min="{{ date('Y-m-d') }}"
                               required>
                    </div>

                    <div class="form-group" id="tanggal_selesai_group" style="display: none;">
                        <label class="form-label">
                            Tanggal Selesai
                            <span class="required">*</span>
                        </label>
                        <input type="date"
                               id="shift_tanggal_selesai"
                               name="tanggal_selesai"
                               class="form-control"
                               min="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Alasan Pengajuan
                        <span class="required">*</span>
                    </label>
                    <textarea id="shift_alasan"
                              name="alasan"
                              class="form-control"
                              placeholder="Jelaskan alasan pengajuan pergantian shift secara detail dan jelas..."
                              required
                              maxlength="500"
                              oninput="updateCharacterCount()"></textarea>
                    <div class="character-count" id="charCount">0 / 500 karakter (minimal 10)</div>
                </div>
            </form>
        </div>
    </div>

    {{-- Form Footer --}}
    <div class="form-footer">
        <button type="submit" form="formShift" class="btn-submit" id="btnSubmit">
            <i class="fas fa-paper-plane"></i>
            Kirim Pengajuan
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle tanggal selesai based on jenis
function toggleTanggalSelesai() {
    const sementara = document.getElementById('jenis_sementara').checked;
    const tanggalSelesaiGroup = document.getElementById('tanggal_selesai_group');
    const tanggalSelesaiInput = document.getElementById('shift_tanggal_selesai');

    if (sementara) {
        tanggalSelesaiGroup.style.display = 'block';
        tanggalSelesaiInput.required = true;
    } else {
        tanggalSelesaiGroup.style.display = 'none';
        tanggalSelesaiInput.required = false;
        tanggalSelesaiInput.value = '';
    }
}

// Update character count
function updateCharacterCount() {
    const textarea = document.getElementById('shift_alasan');
    const charCount = document.getElementById('charCount');
    const length = textarea.value.length;

    charCount.textContent = `${length} / 500 karakter (minimal 10)`;

    if (length < 10) {
        charCount.className = 'character-count error';
    } else if (length > 450) {
        charCount.className = 'character-count warning';
    } else {
        charCount.className = 'character-count';
    }
}

// Update end date min value when start date changes
document.getElementById('shift_tanggal_mulai')?.addEventListener('change', function() {
    const endInput = document.getElementById('shift_tanggal_selesai');
    if (endInput) {
        endInput.setAttribute('min', this.value);
        if (endInput.value && endInput.value < this.value) {
            endInput.value = this.value;
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Touch feedback for mobile
    const submitBtn = document.querySelector('.btn-submit');
    if (submitBtn) {
        submitBtn.addEventListener('touchstart', () => {
            submitBtn.style.transform = 'scale(0.98)';
        });
        submitBtn.addEventListener('touchend', () => {
            submitBtn.style.transform = '';
        });
    }

    // Form validation
    const shiftForm = document.getElementById('formShift');
    if (shiftForm) {
        shiftForm.addEventListener('submit', function(e) {
            const startDate = document.getElementById('shift_tanggal_mulai').value;
            const endDate = document.getElementById('shift_tanggal_selesai').value;
            const alasan = document.getElementById('shift_alasan').value.trim();
            const jenis = document.querySelector('input[name="jenis"]:checked');

            // Validate jenis
            if (!jenis) {
                e.preventDefault();
                alert('Pilih jenis pengajuan (Sementara atau Permanen)');
                return false;
            }

            // Validate dates for sementara
            if (jenis.value === 'sementara') {
                if (!endDate) {
                    e.preventDefault();
                    alert('Tanggal selesai harus diisi untuk pengajuan sementara');
                    return false;
                }

                if (startDate && endDate) {
                    const start = new Date(startDate);
                    const end = new Date(endDate);

                    if (end < start) {
                        e.preventDefault();
                        alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
                        return false;
                    }
                }
            }

            // Validate reason
            if (alasan.length < 10) {
                e.preventDefault();
                alert('Alasan pengajuan minimal 10 karakter');
                return false;
            }

            // Show loading state
            const submitBtn = document.getElementById('btnSubmit');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            submitBtn.disabled = true;

            return true;
        });
    }
});
</script>
@endpush
