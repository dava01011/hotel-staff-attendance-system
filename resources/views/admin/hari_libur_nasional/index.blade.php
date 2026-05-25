@extends('admin.layouts.app')

@section('title', 'Hari Libur Nasional')

@push('styles')
<style>
    .filter-input {
        border: 2px solid #e9ecef; border-radius: 8px;
        font-size: 13px; transition: all 0.2s;
        height: 38px;
    }
    .filter-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.1);
    }
    .filter-label {
        font-size: 11px; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.5px;
        margin-bottom: 4px; display: block;
    }
    .search-wrap {
        position: relative;
    }
    .search-wrap .search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); color: #6c757d; pointer-events: none; font-size: 13px;
    }
    .search-wrap input {
        padding-left: 36px; padding-right: 36px;
    }
    .search-wrap .clear-btn {
        position: absolute; right: 10px; top: 50%;
        transform: translateY(-50%); background: none; border: none;
        color: #adb5bd; cursor: pointer; padding: 2px; display: none; font-size: 13px;
    }
    .search-wrap .clear-btn:hover { color: #dc3545; }
    .search-wrap .clear-btn.show  { display: block; }

    .filter-reset {
        font-size: 12px; color: #dc3545; font-weight: 600;
        text-decoration: none; display: none; align-items: center; gap: 4px;
    }
    .filter-reset:hover { text-decoration: underline; color: #dc3545; }
    .filter-reset.show  { display: inline-flex; }

    .active-filter-chip {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; background: #eff6ff; color: #1d4ed8;
        border-radius: 20px; font-size: 12px; font-weight: 600;
        border: 1px solid #bfdbfe;
    }

    .holiday-date-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 20px;
        background: #eff6ff; color: #1d4ed8;
        font-size: 12px; font-weight: 600;
    }
    .holiday-date-badge .day-name {
        background: #1d4ed8; color: white;
        padding: 1px 7px; border-radius: 10px; font-size: 10px;
    }

    .tipe-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
    }
    .tipe-badge.fixed   { background: #dbeafe; color: #1d4ed8; }
    .tipe-badge.dynamic { background: #fef3c7; color: #92400e; }
    .tipe-badge.manual  { background: #f3e8ff; color: #6b21a8; }

    .stat-card {
        border-radius: 12px; padding: 16px 18px;
        display: flex; align-items: center; gap: 12px;
    }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 18px;
    }
    .stat-num   { font-size: 22px; font-weight: 700; line-height: 1; }
    .stat-label { font-size: 11px; font-weight: 600; opacity: 0.75; margin-top: 2px; }

    .result-info {
        font-size: 13px; color: #64748b;
        padding: 10px 16px; border-bottom: 1px solid #f1f5f9;
        background: #f8fafc; display: none;
    }
    .result-info.show { display: flex; align-items: center; justify-content: space-between; }

    .btn-sm { padding: 6px 12px; transition: all 0.2s; }
    .btn-sm:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .table tbody tr { transition: background-color 0.2s; }

    .no-results { display: none; text-align: center; padding: 50px 20px; }
    .no-results.show { display: block; }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Hari Libur Nasional</h4>
        <small class="text-muted">Kelola tanggal merah & hari libur nasional</small>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#syncHolidaysModal">
            <i class="fas fa-sync-alt"></i> Sync Data
        </button>
        <a href="{{ route('admin.hari-libur-nasional.create') }}"
           class="btn btn-sm btn-primary d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>
</div>

{{-- Summary Stats --}}
@if(isset($summary))
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#eff6ff;color:#1d4ed8;">
            <div class="stat-icon" style="background:#dbeafe;">
                <i class="fas fa-calendar-times"></i>
            </div>
            <div>
                <div class="stat-num">{{ $summary['total_tahun_ini'] ?? 0 }}</div>
                <div class="stat-label">Total {{ $tahun }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#f0fdf4;color:#16a34a;">
            <div class="stat-icon" style="background:#dcfce7;">
                <i class="fas fa-thumbtack"></i>
            </div>
            <div>
                <div class="stat-num">{{ $summary['total_fixed'] ?? 0 }}</div>
                <div class="stat-label">Fixed (Otomatis)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#fffbeb;color:#d97706;">
            <div class="stat-icon" style="background:#fef3c7;">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div>
                <div class="stat-num">{{ $summary['total_dynamic'] ?? 0 }}</div>
                <div class="stat-label">Dynamic</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#fdf4ff;color:#7c3aed;">
            <div class="stat-icon" style="background:#f3e8ff;">
                <i class="fas fa-pen"></i>
            </div>
            <div>
                <div class="stat-num">{{ $summary['total_manual'] ?? 0 }}</div>
                <div class="stat-label">Manual</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Filter Bar --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">

            {{-- Tahun --}}
            <div class="col-6 col-md-2">
                <label class="filter-label">Tahun</label>
                <input type="number" id="filterTahun" class="form-control filter-input"
                       min="2000" max="2100"
                       value="{{ $tahun }}"
                       placeholder="{{ now()->year }}">
            </div>

            {{-- Dari Tanggal --}}
            <div class="col-6 col-md-2">
                <label class="filter-label">Dari Tanggal</label>
                <input type="date" id="filterDari" class="form-control filter-input"
                       value="{{ request('dari') }}">
            </div>

            {{-- Sampai Tanggal --}}
            <div class="col-6 col-md-2">
                <label class="filter-label">Sampai Tanggal</label>
                <input type="date" id="filterSampai" class="form-control filter-input"
                       value="{{ request('sampai') }}">
            </div>

            {{-- Tipe --}}
            <div class="col-6 col-md-2">
                <label class="filter-label">Tipe</label>
                <select id="filterTipe" class="form-select filter-input">
                    <option value="">Semua Tipe</option>
                    <option value="fixed"   {{ request('tipe') === 'fixed'   ? 'selected' : '' }}>Fixed</option>
                    <option value="dynamic" {{ request('tipe') === 'dynamic' ? 'selected' : '' }}>Dynamic</option>
                    <option value="manual"  {{ request('tipe') === 'manual'  ? 'selected' : '' }}>Manual</option>
                </select>
            </div>

            {{-- Nama --}}
            <div class="col-md col-12">
                <label class="filter-label">Cari Nama</label>
                <div class="search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="filterNama" class="form-control filter-input"
                           placeholder="Cari nama hari libur..."
                           value="{{ request('nama') }}">
                    <button class="clear-btn" id="clearNama" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            {{-- Apply + Reset --}}
            <div class="col-auto">
                <label class="filter-label" style="visibility:hidden;">x</label>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-primary btn-sm px-3" id="applyFilter">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.hari-libur-nasional.index') }}"
                       class="filter-reset {{ request()->hasAny(['dari','sampai','tipe','nama']) || request('tahun') ? 'show' : '' }}"
                       id="resetFilter">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>

        </div>

        {{-- Active filter chips --}}
        <div class="d-flex gap-2 flex-wrap mt-2" id="activeChips">
            @if(request('tahun'))
                <span class="active-filter-chip">
                    <i class="fas fa-calendar"></i> Tahun {{ request('tahun') }}
                </span>
            @endif
            @if(request('dari'))
                <span class="active-filter-chip">
                    <i class="fas fa-calendar-day"></i> Dari {{ \Carbon\Carbon::parse(request('dari'))->format('d M Y') }}
                </span>
            @endif
            @if(request('sampai'))
                <span class="active-filter-chip">
                    <i class="fas fa-calendar-day"></i> s/d {{ \Carbon\Carbon::parse(request('sampai'))->format('d M Y') }}
                </span>
            @endif
            @if(request('tipe'))
                <span class="active-filter-chip">
                    <i class="fas fa-tag"></i> {{ ucfirst(request('tipe')) }}
                </span>
            @endif
            @if(request('nama'))
                <span class="active-filter-chip">
                    <i class="fas fa-search"></i> "{{ request('nama') }}"
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card shadow-sm border-0">

    {{-- Result info --}}
    <div class="result-info {{ $hariLibur->total() > 0 ? 'show' : '' }}" id="resultInfo">
        <span>
            Menampilkan <strong>{{ $hariLibur->firstItem() ?? 0 }}</strong>–<strong>{{ $hariLibur->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $hariLibur->total() }}</strong> hari libur
        </span>
        <span class="text-muted" style="font-size:12px;">
            Halaman {{ $hariLibur->currentPage() }} / {{ $hariLibur->lastPage() }}
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Tanggal</th>
                        <th>Nama Hari Libur</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="liburTableBody">
                    @forelse($hariLibur as $item)
                        <tr>
                            <td class="ps-4">
                                {{ $loop->iteration + ($hariLibur->currentPage() - 1) * $hariLibur->perPage() }}
                            </td>
                            <td>
                                <div class="holiday-date-badge">
                                    <span>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</span>
                                    <span class="day-name">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('dddd') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color:#2d3748;">{{ $item->nama }}</div>
                            </td>
                            <td>
                                @php $tipe = $item->tipe ?? 'manual'; @endphp
                                <span class="tipe-badge {{ $tipe }}">
                                    @if($tipe === 'fixed')   <i class="fas fa-thumbtack"></i> Fixed
                                    @elseif($tipe === 'dynamic') <i class="fas fa-sync-alt"></i> Dynamic
                                    @else <i class="fas fa-pen"></i> Manual
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:13px;">
                                    {{ $item->keterangan ?: '-' }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('admin.hari-libur-nasional.edit', $item->id) }}"
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" title="Hapus"
                                    data-bs-toggle="modal"
                                    data-bs-target="#hapusLibur{{ $item->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3" style="opacity:0.3;"></i>
                                    <p class="mb-1 fw-medium">Tidak ada hari libur ditemukan</p>
                                    <small>Coba ubah filter atau
                                        <a href="{{ route('admin.hari-libur-nasional.index') }}">reset filter</a>
                                    </small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hariLibur->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $hariLibur->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Delete Modals --}}
@foreach($hariLibur as $item)
<div class="modal fade" id="hapusLibur{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0" style="background:#fef2f2;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-trash" style="color:#dc2626;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Hapus Hari Libur</h6>
                        <small class="text-muted" style="font-size:11px;">Tindakan tidak bisa dibatalkan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.hari-libur-nasional.destroy', $item->id) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body text-center py-4">
                    <p style="font-size:13px;" class="mb-1">Yakin hapus hari libur:</p>
                    <div class="fw-bold" style="font-size:14px;color:#1e293b;">{{ $item->nama }}</div>
                    <div class="text-muted mt-1" style="font-size:12px;">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-semibold">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Sync Holidays Modal --}}
<div class="modal fade" id="syncHolidaysModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#eff6ff;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-sync-alt" style="color:#2563eb;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Sync Hari Libur</h6>
                        <small class="text-muted" style="font-size:11px;">Ambil data dari kalender dunia</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.hari-libur-nasional.sync') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="filter-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control filter-input" 
                               value="{{ $tahun }}" min="2000" max="2100" required>
                    </div>
                    <div class="mb-0">
                        <label class="filter-label">Negara</label>
                        <select name="country_code" class="form-select filter-input" required>
                            <option value="ID" selected>Indonesia (ID)</option>
                            <option value="US">United States (US)</option>
                            <option value="GB">United Kingdom (GB)</option>
                            <option value="JP">Japan (JP)</option>
                            <option value="SG">Singapore (SG)</option>
                            <option value="MY">Malaysia (MY)</option>
                            <option value="AU">Australia (AU)</option>
                        </select>
                        <small class="text-muted mt-1 d-block" style="font-size:10px;">
                            Data diambil dari Nager.Date Public API
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-sync-alt me-1"></i>Sync Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const filterNama   = document.getElementById('filterNama');
    const clearNama    = document.getElementById('clearNama');
    const applyBtn     = document.getElementById('applyFilter');

    // Show/hide clear button untuk nama
    filterNama.addEventListener('input', function () {
        clearNama.classList.toggle('show', this.value.length > 0);
    });
    if (filterNama.value) clearNama.classList.add('show');

    clearNama.addEventListener('click', function () {
        filterNama.value = '';
        clearNama.classList.remove('show');
        filterNama.focus();
    });

    // Kalau filter tahun berubah, clear range tanggal supaya tidak konflik
    document.getElementById('filterTahun').addEventListener('change', function () {
        if (this.value) {
            document.getElementById('filterDari').value   = '';
            document.getElementById('filterSampai').value = '';
        }
    });

    // Kalau range tanggal diisi, clear tahun
    ['filterDari', 'filterSampai'].forEach(id => {
        document.getElementById(id).addEventListener('change', function () {
            if (this.value) {
                document.getElementById('filterTahun').value = '';
            }
        });
    });

    // Apply filter → submit ke server
    applyBtn.addEventListener('click', function () {
        const params = new URLSearchParams();

        const tahun  = document.getElementById('filterTahun').value.trim();
        const dari   = document.getElementById('filterDari').value;
        const sampai = document.getElementById('filterSampai').value;
        const tipe   = document.getElementById('filterTipe').value;
        const nama   = filterNama.value.trim();

        if (tahun)  params.set('tahun',  tahun);
        if (dari)   params.set('dari',   dari);
        if (sampai) params.set('sampai', sampai);
        if (tipe)   params.set('tipe',   tipe);
        if (nama)   params.set('nama',   nama);

        window.location.href = '{{ route('admin.hari-libur-nasional.index') }}?' + params.toString();
    });

    // Enter pada input nama juga apply
    filterNama.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') applyBtn.click();
    });

    // Toast
    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed;top:20px;right:20px;padding:15px 25px;
            background:${type==='success'?'#28a745':'#dc3545'};color:white;border-radius:8px;
            font-weight:600;font-size:14px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.15);
            animation:slideInRight 0.3s ease-out;display:flex;align-items:center;gap:10px;max-width:420px;`;
        t.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':'exclamation-circle'}"></i><span>${msg}</span>`;
        document.body.appendChild(t);
        setTimeout(()=>{ t.style.opacity='0'; t.style.transition='opacity 0.3s'; setTimeout(()=>t.remove(),300); },3500);
    }

    @if(session('success')) showToast(`{!! session('success') !!}`, 'success'); @endif
    @if(session('error'))   showToast(`{!! session('error') !!}`, 'error');   @endif
});
</script>
@endpush
