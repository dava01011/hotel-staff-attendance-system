@extends('admin.layouts.app')

@section('title', 'Tambah Template Hari Libur')

@push('styles')
<style>
    .tipe-selector {
        display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;
    }
    .tipe-card {
        border: 2px solid #e9ecef; border-radius: 12px; padding: 16px;
        cursor: pointer; transition: all 0.2s; background: white; text-align: center;
    }
    .tipe-card:hover { border-color: #0d6efd; background: #eff6ff; }
    .tipe-card.selected-fixed   { border-color: #1d4ed8; background: #eff6ff; }
    .tipe-card.selected-dynamic { border-color: #d97706; background: #fffbeb; }
    .tipe-card input[type="radio"] { display: none; }
    .tipe-card .tipe-icon {
        font-size: 24px; margin-bottom: 8px;
    }
    .tipe-card .tipe-title { font-weight: 700; font-size: 14px; color: #1e293b; }
    .tipe-card .tipe-desc  { font-size: 11px; color: #718096; margin-top: 4px; }

    .section-fixed   { display: block; }
    .section-dynamic { display: none; }

    .tahun-row {
        display: grid; grid-template-columns: 100px 1fr 36px; gap: 8px; align-items: center;
        margin-bottom: 8px;
    }
    .btn-remove-tahun {
        width: 32px; height: 32px; border-radius: 8px; border: none;
        background: #fee2e2; color: #dc2626; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; transition: all 0.2s;
    }
    .btn-remove-tahun:hover { background: #dc2626; color: white; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.hari-libur-template.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1">Tambah Template Hari Libur</h4>
        <small class="text-muted">Buat template untuk generate hari libur otomatis</small>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 pt-4 px-4" style="background:white;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-clipboard-list" style="color:#1d4ed8;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Data Template</h6>
                        <small class="text-muted" style="font-size:11px;">Pilih tipe dan isi detail template</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.hari-libur-template.store') }}" method="POST" id="templateForm">
                @csrf
                <div class="card-body pt-4">

                    {{-- Nama --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Nama Template <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               style="font-size:13px;"
                               placeholder="contoh: Hari Kemerdekaan RI"
                               value="{{ old('nama') }}" required>
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Tipe Selector --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Tipe Template <span class="text-danger">*</span>
                        </label>
                        <div class="tipe-selector">
                            <label class="tipe-card selected-fixed" id="card-fixed" onclick="selectTipe('fixed')">
                                <input type="radio" name="tipe" value="fixed" checked>
                                <div class="tipe-icon">📌</div>
                                <div class="tipe-title">Fixed</div>
                                <div class="tipe-desc">Tanggal tetap setiap tahun<br>(contoh: 17 Agustus)</div>
                            </label>
                            <label class="tipe-card" id="card-dynamic" onclick="selectTipe('dynamic')">
                                <input type="radio" name="tipe" value="dynamic">
                                <div class="tipe-icon">🔄</div>
                                <div class="tipe-title">Dynamic</div>
                                <div class="tipe-desc">Tanggal berubah tiap tahun<br>(contoh: Lebaran, Nyepi)</div>
                            </label>
                        </div>
                    </div>

                    {{-- Section Fixed --}}
                    <div class="section-fixed" id="sectionFixed">
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
                                       style="font-size:13px;"
                                       min="1" max="31" placeholder="1-31"
                                       value="{{ old('hari') }}">
                                @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section Dynamic --}}
                    <div class="section-dynamic" id="sectionDynamic">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Tanggal per Tahun <span class="text-danger">*</span>
                        </label>
                        <div id="tahunRows"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="addTahunRow()">
                            <i class="fas fa-plus me-1"></i> Tambah Tahun
                        </button>
                        <input type="hidden" name="tanggal_json" id="tanggalJson">
                        <div class="form-text mt-2" style="font-size:11px;color:#718096;">
                            Masukkan pasangan tahun dan tanggal. Klik Tambah Tahun untuk menambah lebih banyak.
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Keterangan --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">Keterangan</label>
                        <textarea name="keterangan"
                                  class="form-control"
                                  style="font-size:13px;" rows="2"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    </div>

                </div>
                <div class="card-footer border-0 bg-white d-flex justify-content-end gap-2 pb-4 px-4">
                    <a href="{{ route('admin.hari-libur-template.index') }}" class="btn btn-light btn-sm px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="submitBtn">
                        <i class="fas fa-save me-1"></i> Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentTipe = 'fixed';

    function selectTipe(tipe) {
        currentTipe = tipe;

        document.getElementById('card-fixed').className   = 'tipe-card' + (tipe === 'fixed'   ? ' selected-fixed'   : '');
        document.getElementById('card-dynamic').className = 'tipe-card' + (tipe === 'dynamic' ? ' selected-dynamic' : '');
        document.getElementById('sectionFixed').style.display   = tipe === 'fixed'   ? 'block' : 'none';
        document.getElementById('sectionDynamic').style.display = tipe === 'dynamic' ? 'block' : 'none';

        // Set radio value
        document.querySelector(`input[name="tipe"][value="${tipe}"]`).checked = true;

        // Ensure at least 1 row for dynamic
        if (tipe === 'dynamic' && document.querySelectorAll('.tahun-row').length === 0) {
            addTahunRow();
        }
    }

    function addTahunRow(tahun = '', tanggal = '') {
        const container = document.getElementById('tahunRows');
        const idx       = container.children.length;
        const row       = document.createElement('div');
        row.className   = 'tahun-row';
        row.innerHTML   = `
            <input type="number" class="form-control tahun-input" style="font-size:13px;"
                   placeholder="Tahun" min="2020" max="2100" value="${tahun}">
            <input type="date" class="form-control tanggal-input" style="font-size:13px;"
                   value="${tanggal}">
            <button type="button" class="btn-remove-tahun" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(row);
    }

    // Sebelum submit, build tanggal_json dari rows
    document.getElementById('templateForm').addEventListener('submit', function (e) {
        if (currentTipe === 'dynamic') {
            const rows    = document.querySelectorAll('.tahun-row');
            const result  = {};
            let valid     = true;

            rows.forEach(row => {
                const tahun   = row.querySelector('.tahun-input').value.trim();
                const tanggal = row.querySelector('.tanggal-input').value.trim();
                if (tahun && tanggal) result[tahun] = tanggal;
                else valid = false;
            });

            if (Object.keys(result).length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 pasangan tahun dan tanggal.');
                return;
            }

            document.getElementById('tanggalJson').value = JSON.stringify(result);
        }
    });

    // Init: tambah 2 row default untuk dynamic
    addTahunRow({{ now()->year }}, '');
    addTahunRow({{ now()->year + 1 }}, '');
</script>
@endpush
