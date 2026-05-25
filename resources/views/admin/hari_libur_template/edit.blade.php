@extends('admin.layouts.app')

@section('title', 'Edit Template Hari Libur')

@push('styles')
<style>
    .tahun-row {
        display: grid; grid-template-columns: 100px 1fr 36px; gap: 8px;
        align-items: center; margin-bottom: 8px;
    }
    .btn-remove-tahun {
        width: 32px; height: 32px; border-radius: 8px; border: none;
        background: #fee2e2; color: #dc2626; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; transition: all 0.2s;
    }
    .btn-remove-tahun:hover { background: #dc2626; color: white; }
    .tipe-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;
    }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.hari-libur-template.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1">Edit Template Hari Libur</h4>
        <small class="text-muted">{{ $template->nama }}</small>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 pt-4 px-4" style="background:#fffbeb;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fef3c7;
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-edit" style="color:#d97706;font-size:15px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size:14px;">Edit Template</h6>
                            <small class="text-muted" style="font-size:11px;">{{ $template->nama }}</small>
                        </div>
                    </div>
                    <span class="tipe-pill {{ $template->tipe === 'fixed' ? 'bg-primary text-white' : '' }}"
                          style="{{ $template->tipe === 'dynamic' ? 'background:#fef3c7;color:#92400e;' : '' }}">
                        @if($template->tipe === 'fixed')
                            <i class="fas fa-thumbtack"></i> Fixed
                        @else
                            <i class="fas fa-sync-alt"></i> Dynamic
                        @endif
                    </span>
                </div>
            </div>

            <form action="{{ route('admin.hari-libur-template.update', $template->id) }}" method="POST"
                  id="editTemplateForm">
                @csrf @method('PUT')
                <div class="card-body pt-4">

                    {{-- Nama (readonly, unique) --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Nama Template</label>
                        <input type="text" class="form-control" style="font-size:13px;background:#f8f9fa;"
                               value="{{ $template->nama }}" disabled>
                        <div class="form-text" style="font-size:11px;">Nama template tidak dapat diubah.</div>
                    </div>

                    @if($template->tipe === 'fixed')
                        {{-- Fixed fields --}}
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Bulan <span class="text-danger">*</span>
                                </label>
                                <select name="bulan" class="form-select @error('bulan') is-invalid @enderror"
                                        style="font-size:13px;" required>
                                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                                        <option value="{{ $i+1 }}"
                                            {{ old('bulan', $template->bulan) == $i+1 ? 'selected' : '' }}>
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
                                       min="1" max="31"
                                       value="{{ old('hari', $template->hari) }}" required>
                                @error('hari') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    @else
                        {{-- Dynamic fields --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Tanggal per Tahun <span class="text-danger">*</span>
                            </label>
                            <div id="tahunRows">
                                @if($template->tanggal_per_tahun)
                                    @foreach((array)$template->tanggal_per_tahun as $yr => $tgl)
                                        <div class="tahun-row">
                                            <input type="number" class="form-control tahun-input"
                                                   style="font-size:13px;" min="2020" max="2100"
                                                   placeholder="Tahun" value="{{ $yr }}">
                                            <input type="date" class="form-control tanggal-input"
                                                   style="font-size:13px;" value="{{ $tgl }}">
                                            <button type="button" class="btn-remove-tahun"
                                                    onclick="this.parentElement.remove()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                    onclick="addTahunRow()">
                                <i class="fas fa-plus me-1"></i> Tambah Tahun
                            </button>
                            <input type="hidden" name="tanggal_json" id="tanggalJson">
                        </div>
                    @endif

                    {{-- Keterangan --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">Keterangan</label>
                        <textarea name="keterangan" class="form-control" style="font-size:13px;" rows="2">{{ old('keterangan', $template->keterangan) }}</textarea>
                    </div>

                </div>
                <div class="card-footer border-0 d-flex justify-content-end gap-2 pb-4 px-4"
                     style="background:#fffbeb;">
                    <a href="{{ route('admin.hari-libur-template.index') }}" class="btn btn-light btn-sm px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-sm px-4 fw-semibold"
                            style="background:#fbbf24;color:#1e293b;border:none;">
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
    function addTahunRow(tahun = '', tanggal = '') {
        const container = document.getElementById('tahunRows');
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'tahun-row';
        row.innerHTML = `
            <input type="number" class="form-control tahun-input" style="font-size:13px;"
                   placeholder="Tahun" min="2020" max="2100" value="${tahun}">
            <input type="date" class="form-control tanggal-input" style="font-size:13px;" value="${tanggal}">
            <button type="button" class="btn-remove-tahun" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(row);
    }

    @if($template->tipe === 'dynamic')
    document.getElementById('editTemplateForm').addEventListener('submit', function (e) {
        const rows   = document.querySelectorAll('.tahun-row');
        const result = {};
        rows.forEach(row => {
            const tahun   = row.querySelector('.tahun-input').value.trim();
            const tanggal = row.querySelector('.tanggal-input').value.trim();
            if (tahun && tanggal) result[tahun] = tanggal;
        });
        if (Object.keys(result).length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 pasangan tahun dan tanggal.');
            return;
        }
        document.getElementById('tanggalJson').value = JSON.stringify(result);
    });
    @endif
</script>
@endpush
