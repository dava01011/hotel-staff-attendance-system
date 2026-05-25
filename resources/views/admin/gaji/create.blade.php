@extends('admin.layouts.app')

@section('title', 'Hitung Gaji')

@push('styles')
<style>
    /* ── Form Card ──────────────────────────────────────────── */
    .form-section {
        background: white;
        border: 1px solid #eaecf0;
        border-radius: 14px;
        padding: 28px 30px;
    }

    .form-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: #1e293b;
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,.1);
    }

    /* ── Preview Card ───────────────────────────────────────── */
    .preview-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
    }

    .preview-title {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 14px;
    }

    .preview-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
    }

    .preview-row:last-child { border-bottom: none; }

    .preview-row .label { color: #64748b; }
    .preview-row .value { font-weight: 600; color: #1e293b; }

    .preview-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0 0;
        margin-top: 4px;
        border-top: 2px solid #e2e8f0;
        font-size: 14px;
    }

    .preview-total .label { font-weight: 700; color: #334155; }
    .preview-total .value { font-size: 18px; font-weight: 800; color: #0d6efd; }

    /* ── Info Box ───────────────────────────────────────────── */
    .info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 14px 16px;
        font-size: 13px;
        color: #1e40af;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .info-box i { margin-top: 1px; flex-shrink: 0; }

    /* ── Submit Button ──────────────────────────────────────── */
    .btn-hitung {
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 28px;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        justify-content: center;
    }

    .btn-hitung:hover {
        background: #0b5ed7;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13,110,253,.3);
    }

    /* ── Back Link ──────────────────────────────────────────── */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 16px;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #495057;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-back:hover { border-color: #adb5bd; background: #f8f9fa; color: #495057; }

    /* ── Month/Year Select grid ─────────────────────────────── */
    .period-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 576px) {
        .form-section  { padding: 20px 18px; }
        .period-grid   { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Hitung Gaji</h4>
            <small class="text-muted">Kalkulasi gaji harian karyawan per periode</small>
        </div>
        <a href="{{ route('admin.gaji.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-4">

        {{-- ── Kiri: Form ─────────────────────────────────── --}}
        <div class="col-lg-7">
            <div class="form-section shadow-sm">
                <div class="form-section-title">
                    <i class="fas fa-calculator text-primary"></i>
                    Parameter Perhitungan
                </div>

                <form action="{{ route('admin.gaji.hitung') }}" method="POST" id="gajiForm">
                    @csrf

                    {{-- Karyawan --}}
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-user me-1 text-muted"></i> Karyawan
                        </label>
                        <select name="karyawan_id" class="form-select" required id="selectKaryawan">
                            <option value="">— Pilih Karyawan —</option>
                            @foreach($karyawan as $k)
                                <option value="{{ $k->id }}"
                                        data-jabatan="{{ $k->jabatan->nama_jabatan ?? '-' }}"
                                        data-gaji="{{ $k->jabatan->gaji_harian ?? 0 }}"
                                        {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->user->nama }} — {{ $k->jabatan->nama_jabatan ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Periode --}}
                    <div class="period-grid mb-4">
                        <div>
                            <label class="form-label">
                                <i class="fas fa-calendar me-1 text-muted"></i> Bulan
                            </label>
                            <select name="bulan" class="form-select" required id="selectBulan">
                                @foreach(range(1, 12) as $b)
                                    <option value="{{ $b }}"
                                        {{ (old('bulan', now('Asia/Jakarta')->month) == $b) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($b)->locale('id')->monthName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-1 text-muted"></i> Tahun
                            </label>
                            <input type="number"
                                   name="tahun"
                                   id="inputTahun"
                                   class="form-control"
                                   value="{{ old('tahun', now('Asia/Jakarta')->year) }}"
                                   min="2020"
                                   max="{{ now()->year + 1 }}"
                                   required>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="info-box mb-4">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            Sistem akan menghitung otomatis berdasarkan data absensi karyawan pada periode yang dipilih.
                            Gaji dihitung dari <strong>total hari hadir × gaji harian jabatan</strong>.
                        </div>
                    </div>

                    <button type="submit" class="btn-hitung">
                        <i class="fas fa-calculator"></i>
                        Hitung & Simpan Gaji
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Kanan: Preview & Info ───────────────────────── --}}
        <div class="col-lg-5">

            {{-- Preview Kalkulasi --}}
            <div class="form-section shadow-sm mb-4" id="previewSection" style="display:none;">
                <div class="form-section-title">
                    <i class="fas fa-eye text-violet" style="color:#7c3aed;"></i>
                    Preview Kalkulasi
                </div>
                <div class="preview-card">
                    <div class="preview-title">Estimasi Gaji</div>
                    <div class="preview-row">
                        <span class="label">Karyawan</span>
                        <span class="value" id="prevNama">—</span>
                    </div>
                    <div class="preview-row">
                        <span class="label">Jabatan</span>
                        <span class="value" id="prevJabatan">—</span>
                    </div>
                    <div class="preview-row">
                        <span class="label">Periode</span>
                        <span class="value" id="prevPeriode">—</span>
                    </div>
                    <div class="preview-row">
                        <span class="label">Gaji Harian</span>
                        <span class="value" id="prevGajiHarian">—</span>
                    </div>
                    <div class="preview-row">
                        <span class="label" style="font-size:11px; color:#94a3b8;">* Hari hadir dihitung saat form disubmit</span>
                        <span></span>
                    </div>
                </div>
            </div>

            {{-- Panduan --}}
            <div class="form-section shadow-sm">
                <div class="form-section-title">
                    <i class="fas fa-book-open" style="color:#f59e0b;"></i>
                    Panduan
                </div>
                <div style="font-size:13px; color:#475569; line-height:1.8;">
                    <div class="d-flex gap-2 mb-2">
                        <span style="color:#3b82f6; font-weight:700; font-size:15px; line-height:1.4;">1.</span>
                        <span>Pilih karyawan yang akan dihitung gajinya</span>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <span style="color:#3b82f6; font-weight:700; font-size:15px; line-height:1.4;">2.</span>
                        <span>Tentukan periode bulan dan tahun</span>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <span style="color:#3b82f6; font-weight:700; font-size:15px; line-height:1.4;">3.</span>
                        <span>Sistem akan mengambil data absensi dengan status <strong>hadir</strong></span>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <span style="color:#3b82f6; font-weight:700; font-size:15px; line-height:1.4;">4.</span>
                        <span>Total gaji = hari hadir × gaji harian jabatan</span>
                    </div>
                    <div class="d-flex gap-2">
                        <span style="color:#3b82f6; font-weight:700; font-size:15px; line-height:1.4;">5.</span>
                        <span>Hasil tersimpan dan slip gaji dapat diunduh</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectKaryawan = document.getElementById('selectKaryawan');
    const selectBulan    = document.getElementById('selectBulan');
    const inputTahun     = document.getElementById('inputTahun');
    const previewSection = document.getElementById('previewSection');
    const prevNama       = document.getElementById('prevNama');
    const prevJabatan    = document.getElementById('prevJabatan');
    const prevPeriode    = document.getElementById('prevPeriode');
    const prevGajiHarian = document.getElementById('prevGajiHarian');

    const bulanNames = {
        1:'Januari', 2:'Februari', 3:'Maret', 4:'April',
        5:'Mei', 6:'Juni', 7:'Juli', 8:'Agustus',
        9:'September', 10:'Oktober', 11:'November', 12:'Desember'
    };

    function updatePreview() {
        const opt = selectKaryawan.options[selectKaryawan.selectedIndex];

        if (!opt || !opt.value) {
            previewSection.style.display = 'none';
            return;
        }

        const nama       = opt.text.split(' — ')[0];
        const jabatan    = opt.getAttribute('data-jabatan') || '-';
        const gajiHarian = parseInt(opt.getAttribute('data-gaji') || '0');
        const bulan      = bulanNames[parseInt(selectBulan.value)];
        const tahun      = inputTahun.value;

        prevNama.textContent       = nama;
        prevJabatan.textContent    = jabatan;
        prevPeriode.textContent    = bulan + ' ' + tahun;
        prevGajiHarian.textContent = 'Rp ' + gajiHarian.toLocaleString('id-ID');

        previewSection.style.display = '';
    }

    selectKaryawan.addEventListener('change', updatePreview);
    selectBulan.addEventListener('change',    updatePreview);
    inputTahun.addEventListener('input',      updatePreview);

    // init if old() value exists
    updatePreview();

    /* ── Toast ──────────────────────────────────────────────── */
    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `
            position:fixed; top:20px; right:20px;
            padding:14px 22px;
            background:${type === 'success' ? '#16a34a' : '#dc2626'};
            color:white; border-radius:8px; font-weight:600; font-size:13px;
            z-index:9999; box-shadow:0 4px 14px rgba(0,0,0,.15);
            animation:slideInRight .3s ease-out;
            display:flex; align-items:center; gap:10px;
        `;
        t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span>`;
        document.body.appendChild(t);
        setTimeout(() => {
            t.style.animation = 'slideInRight .3s ease-out reverse';
            setTimeout(() => t.remove(), 300);
        }, 3000);
    }

    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
});
</script>

<style>
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
</style>
@endpush
