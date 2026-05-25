@extends('admin.layouts.app')

@section('title', 'Tambah Hari Libur Nasional')

@push('styles')
<style>
    .tipe-selector {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px;
    }
    .tipe-card {
        border: 2px solid #e9ecef; border-radius: 12px; padding: 14px 10px;
        cursor: pointer; transition: all 0.2s; background: white; text-align: center;
    }
    .tipe-card:hover { border-color: #0d6efd; background: #f8faff; }
    .tipe-card.selected-fixed   { border-color: #1d4ed8; background: #eff6ff; }
    .tipe-card.selected-dynamic { border-color: #d97706; background: #fffbeb; }
    .tipe-card.selected-manual  { border-color: #7c3aed; background: #fdf4ff; }
    .tipe-card input[type="radio"] { display: none; }
    .tipe-card .tipe-icon  { font-size: 22px; margin-bottom: 6px; }
    .tipe-card .tipe-title { font-weight: 700; font-size: 13px; color: #1e293b; }
    .tipe-card .tipe-desc  { font-size: 10px; color: #718096; margin-top: 3px; line-height: 1.4; }

    .section-form { display: none; }
    .section-form.show { display: block; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.hari-libur-nasional.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1">Tambah Hari Libur Nasional</h4>
        <small class="text-muted">Pilih tipe dan isi detail hari libur</small>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 pt-4 px-4" style="background:white;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-calendar-plus" style="color:#1d4ed8;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Data Hari Libur</h6>
                        <small class="text-muted" style="font-size:11px;">Isi detail hari libur nasional</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.hari-libur-nasional.store') }}" method="POST">
                @csrf
                <div class="card-body pt-4">

                    {{-- Tipe Selector --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Tipe <span class="text-danger">*</span>
                        </label>
                        <div class="tipe-selector">
                            <label class="tipe-card" id="card-fixed" onclick="selectTipe('fixed')">
                                <input type="radio" name="tipe" value="fixed">
                                <div class="tipe-icon">📌</div>
                                <div class="tipe-title">Fixed</div>
                                <div class="tipe-desc">Tanggal tetap tiap tahun. Auto-generate.</div>
                            </label>
                            <label class="tipe-card" id="card-dynamic" onclick="selectTipe('dynamic')">
                                <input type="radio" name="tipe" value="dynamic">
                                <div class="tipe-icon">🔄</div>
                                <div class="tipe-title">Dynamic</div>
                                <div class="tipe-desc">Tanggal berubah tiap tahun (Lebaran, dll).</div>
                            </label>
                            <label class="tipe-card selected-manual" id="card-manual" onclick="selectTipe('manual')">
                                <input type="radio" name="tipe" value="manual" checked>
                                <div class="tipe-icon">✏️</div>
                                <div class="tipe-title">Manual</div>
                                <div class="tipe-desc">Input tanggal spesifik satu kali.</div>
                            </label>
                        </div>
                    </div>

                    {{-- Nama (semua tipe) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Nama Hari Libur <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               style="font-size:13px;"
                               placeholder="contoh: Hari Raya Idul Fitri"
                               value="{{ old('nama') }}" required>
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Section Fixed --}}
                    <div class="section-form" id="section-fixed">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Bulan <span class="text-danger">*</span>
                                </label>
                                <select name="bulan"
                                        class="form-select @error('bulan') is-invalid @enderror"
                                        style="font-size:13px;">
                                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                                        <option value="{{ $i+1 }}" {{ old('bulan') == $i+1 ? 'selected' : '' }}>
                                            {{ $bln }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Tanggal (hari ke-) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="hari"
                                       class="form-control @error('hari') is-invalid @enderror"
                                       style="font-size:13px;" min="1" max="31"
                                       placeholder="1 – 31" value="{{ old('hari') }}">
                                @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="alert border-0 mt-3 mb-0 p-2"
                             style="background:#eff6ff;font-size:12px;border-radius:8px;">
                            <i class="fas fa-info-circle me-1" style="color:#1d4ed8;"></i>
                            Fixed holiday akan otomatis di-generate setiap tahun.
                        </div>
                    </div>

                    {{-- Section Dynamic / Manual: tanggal spesifik --}}
                    <div class="section-form show" id="section-dynamic-manual">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Tanggal <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal"
                               class="form-control @error('tanggal') is-invalid @enderror"
                               style="font-size:13px;"
                               value="{{ old('tanggal') }}">
                        @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text" style="font-size:11px;" id="dayPreview"></div>
                    </div>

                    <hr class="my-3">

                    {{-- Keterangan --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">Keterangan</label>
                        <textarea name="keterangan" class="form-control" style="font-size:13px;" rows="2"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    </div>

                </div>
                <div class="card-footer border-0 bg-white d-flex justify-content-end gap-2 pb-4 px-4">
                    <a href="{{ route('admin.hari-libur-nasional.index') }}" class="btn btn-light btn-sm px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function selectTipe(tipe) {
        ['fixed','dynamic','manual'].forEach(t => {
            document.getElementById('card-' + t).className = 'tipe-card' + (t === tipe ? ' selected-' + t : '');
        });
        document.querySelector(`input[name="tipe"][value="${tipe}"]`).checked = true;

        document.getElementById('section-fixed').classList.toggle('show', tipe === 'fixed');
        document.getElementById('section-dynamic-manual').classList.toggle('show', tipe !== 'fixed');
    }

    document.querySelector('input[name="tanggal"]')?.addEventListener('change', function () {
        if (!this.value) return;
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const d    = new Date(this.value);
        document.getElementById('dayPreview').textContent = '📅 ' + days[d.getDay()];
    });

    // Init
    selectTipe('manual');
</script>
@endpush
