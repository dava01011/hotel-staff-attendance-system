@extends('karyawan.layout.fullscreen')

@section('title', 'Ajukan Cuti')

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
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">
    {{-- Scrollable Content --}}
    <div class="pengajuan-content">
        {{-- Form Pengajuan Cuti --}}
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-edit"></i>
                <span>Form Pengajuan Cuti</span>
            </div>

            <form method="POST" action="{{ route('karyawan.pengajuan.cuti') }}" id="formCuti" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">
                        Jenis Cuti
                        <span class="required">*</span>
                    </label>
                    <select name="jenis_id" id="jenis_cuti" class="form-control" required>
                        <option value="">-- Pilih Jenis Cuti --</option>
                        @foreach($jenisCuti as $jenis)
                            <option value="{{ $jenis->id }}"
                                    data-max="{{ $jenis->max_hari }}"
                                    data-file="{{ $jenis->butuh_file }}">
                                {{ $jenis->nama }}
                                @if($jenis->max_hari)
                                    (Maks {{ $jenis->max_hari }} hari)
                                @endif
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
                               id="cuti_tanggal_mulai"
                               name="tanggal_mulai"
                               class="form-control"
                               required
                               onchange="calculateCutiDuration()">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Tanggal Selesai
                            <span class="required">*</span>
                        </label>
                        <input type="date"
                               id="cuti_tanggal_selesai"
                               name="tanggal_selesai"
                               class="form-control"
                               required
                               onchange="calculateCutiDuration()">
                    </div>
                </div>

                <div class="duration-info" id="cutiDuration">
                    <i class="fas fa-calendar-check"></i>
                    <strong id="cutiDurationText">Durasi: - hari</strong>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Alasan Cuti
                        <span class="required">*</span>
                    </label>
                    <textarea id="cuti_alasan"
                              name="alasan"
                              class="form-control"
                              placeholder="Jelaskan alasan pengajuan cuti Anda secara detail dan jelas..."
                              required
                              maxlength="500"
                              oninput="updateCharacterCount()"></textarea>
                    <div class="character-count" id="charCount">0 / 500 karakter (minimal 10)</div>
                </div>

                <div class="form-group" id="file_pendukung_group" >
                    <label class="form-label">
                        File Pendukung
                        <span class="required" id="file_required">*</span>
                    </label>
                    <input type="file"
                           name="file_pendukung"
                           id="file_pendukung"
                           class="form-control"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small style="color: #718096; font-size: 12px; display: block; margin-top: 5px;">
                        Format: PDF, JPG, PNG. Maksimal 2MB
                    </small>
                </div>
            </form>
        </div>
    </div>

    {{-- Form Footer --}}
    <div class="form-footer">
        <button type="submit" form="formCuti" class="btn-submit" id="btnSubmit">
            <i class="fas fa-paper-plane"></i>
            Kirim Pengajuan
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show/hide file upload based on jenis cuti
document.getElementById('jenis_cuti')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const butuhFile = selectedOption.getAttribute('data-file') === '1';
    const fileGroup = document.getElementById('file_pendukung_group');
    const fileInput = document.getElementById('file_pendukung');
    const fileRequired = document.getElementById('file_required');

    if (butuhFile) {
        fileGroup.style.display = 'block';
        fileInput.required = true;
        fileRequired.style.display = 'inline';
    } else {
        fileGroup.style.display = 'none';
        fileInput.required = false;
        fileRequired.style.display = 'none';
    }

    // Recalculate duration if dates are filled
    calculateCutiDuration();
});

// Calculate cuti duration
function calculateCutiDuration() {
    const startDate = document.getElementById('cuti_tanggal_mulai').value;
    const endDate = document.getElementById('cuti_tanggal_selesai').value;
    const durationInfo = document.getElementById('cutiDuration');
    const jenisSelect = document.getElementById('jenis_cuti');

    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = end - start;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

        if (diffDays > 0) {
            document.getElementById('cutiDurationText').textContent = `Durasi: ${diffDays} hari`;
            durationInfo.classList.add('show');

            // Check max hari jika jenis cuti dipilih
            if (jenisSelect.value) {
                const maxHari = jenisSelect.options[jenisSelect.selectedIndex].getAttribute('data-max');
                if (maxHari && diffDays > parseInt(maxHari)) {
                    durationInfo.style.background = 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)';
                    durationInfo.style.borderLeftColor = '#dc3545';
                    document.getElementById('cutiDurationText').innerHTML =
                        `Durasi: ${diffDays} hari <small style="color: #dc3545;">(⚠️ Melebihi batas maksimal ${maxHari} hari)</small>`;
                } else {
                    durationInfo.style.background = 'linear-gradient(135deg, #e8f4fd 0%, #d3eafd 100%)';
                    durationInfo.style.borderLeftColor = '#FF6B35';
                }
            }
        } else {
            durationInfo.classList.remove('show');
        }
    }
}

// Update character count
function updateCharacterCount() {
    const textarea = document.getElementById('cuti_alasan');
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

// Set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const minDateStr = today.toISOString().split('T')[0];
    const startInput = document.getElementById('cuti_tanggal_mulai');
    const endInput = document.getElementById('cuti_tanggal_selesai');

    if (startInput) startInput.setAttribute('min', minDateStr);
    if (endInput) endInput.setAttribute('min', minDateStr);

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
    const cutiForm = document.getElementById('formCuti');
    if (cutiForm) {
        cutiForm.addEventListener('submit', function(e) {
            const startDate = document.getElementById('cuti_tanggal_mulai').value;
            const endDate = document.getElementById('cuti_tanggal_selesai').value;
            const alasan = document.getElementById('cuti_alasan').value.trim();
            const jenisSelect = document.getElementById('jenis_cuti');

            // Validate dates
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);

                if (end < start) {
                    e.preventDefault();
                    alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
                    return false;
                }

                const diffTime = end - start;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                // Check max hari
                if (jenisSelect.value) {
                    const maxHari = jenisSelect.options[jenisSelect.selectedIndex].getAttribute('data-max');
                    if (maxHari && diffDays > parseInt(maxHari)) {
                        e.preventDefault();
                        alert(`Maksimal cuti untuk jenis ini adalah ${maxHari} hari`);
                        return false;
                    }
                }
            }

            // Validate reason
            if (alasan.length < 10) {
                e.preventDefault();
                alert('Alasan cuti minimal 10 karakter');
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

// Update end date min value when start date changes
document.getElementById('cuti_tanggal_mulai')?.addEventListener('change', function() {
    const endInput = document.getElementById('cuti_tanggal_selesai');
    if (endInput) {
        endInput.setAttribute('min', this.value);
        if (endInput.value && endInput.value < this.value) {
            endInput.value = this.value;
        }
        calculateCutiDuration();
    }
});
</script>
@endpush
