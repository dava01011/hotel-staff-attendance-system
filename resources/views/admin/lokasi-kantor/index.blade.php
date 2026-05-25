{{-- resources/views/admin/lokasi-kantor/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Lokasi Kantor')

@push('styles')
<style>
    .search-container {
        position: relative; flex: 1; max-width: 400px;
    }
    .search-input {
        padding-left: 45px; border-radius: 25px;
        border: 2px solid #e9ecef; transition: all 0.3s;
    }
    .search-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.1);
    }
    .search-icon {
        position: absolute; left: 15px; top: 50%;
        transform: translateY(-50%); color: #6c757d; pointer-events: none;
    }
    .clear-search {
        position: absolute; right: 15px; top: 50%;
        transform: translateY(-50%); background: none; border: none;
        color: #6c757d; cursor: pointer; padding: 5px; display: none;
    }
    .clear-search:hover { color: #dc3545; }
    .clear-search.show  { display: block; }

    .coord-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
        background: #e0f2fe; color: #0369a1;
    }

    .radius-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
        background: #fef3c7; color: #92400e;
    }

    .btn-sm { padding: 6px 12px; transition: all 0.2s; }
    .btn-sm:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .table tbody tr { transition: background-color 0.2s; }

    .no-results { display: none; text-align: center; padding: 50px 20px; }
    .no-results.show { display: block; }

    /* Input number styling */
    .coord-input {
        font-size: 13px;
        font-family: 'Courier New', monospace;
    }

    .coord-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.1);
    }

    /* Map container */
    .map-container {
        width: 100%; height: 300px; border-radius: 10px;
        border: 2px solid #e9ecef; margin-bottom: 15px;
        background: #f8f9fa;
        display: flex; align-items: center; justify-content: center;
        color: #6c757d;
    }

    .map-container i { font-size: 24px; opacity: 0.5; }

    /* Copy button */
    .btn-copy {
        padding: 4px 8px; font-size: 11px; border: 1px solid #dee2e6;
        background: #f8f9fa; color: #6c757d; border-radius: 4px;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-copy:hover {
        background: #0d6efd; color: white; border-color: #0d6efd;
    }

    .coord-group {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }

    @media (max-width: 576px) {
        .coord-group { grid-template-columns: 1fr; }
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Lokasi Kantor</h4>
        <small class="text-muted">Kelola lokasi-lokasi kantor untuk geofencing absensi</small>
    </div>
    <button class="btn btn-sm btn-primary d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#tambahLokasiKantor">
        <i class="fas fa-plus"></i> Tambah Lokasi
    </button>
</div>

{{-- Search & Stats --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="form-control search-input" id="searchInput"
                    placeholder="Cari nama lokasi..." autocomplete="off">
                <button class="clear-search" id="clearSearch">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="ms-auto d-flex gap-2">
                <span class="badge bg-info px-3 py-2" style="font-size:12px;">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    {{ $lokasiKantor->count() }} lokasi
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Nama Lokasi</th>
                        <th>Koordinat</th>
                        <th>Radius</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="lokasiKantorBody">
                    @forelse($lokasiKantor as $item)
                        <tr data-nama="{{ strtolower($item->nama_lokasi) }}">
                            <td class="ps-4">{{ $loop->iteration }}</td>

                            <td>
                                <div class="fw-semibold" style="color:#2d3748;font-size:14px;">
                                    {{ $item->nama_lokasi }}
                                </div>
                                <small class="text-muted" style="font-size:11px;">
                                    ID: {{ $item->id }}
                                </small>
                            </td>

                            <td>
                                <span class="coord-badge">
                                    <i class="fas fa-map-pin"></i>
                                    {{ number_format($item->latitude, 6) }}, {{ number_format($item->longitude, 6) }}
                                </span>
                            </td>

                            <td>
                                <span class="radius-badge">
                                    <i class="fas fa-ruler"></i>
                                    @if($item->radius >= 1000)
                                        {{ number_format($item->radius / 1000, 2) }} km
                                    @else
                                        {{ $item->radius }} m
                                    @endif
                                </span>
                            </td>

                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-info btn-view" title="Lihat di Peta"
                                        data-id="{{ $item->id }}"
                                        data-val-nama="{{ $item->nama_lokasi }}"
                                        data-val-lat="{{ $item->latitude }}"
                                        data-val-lng="{{ $item->longitude }}"
                                        data-val-radius="{{ $item->radius }}">
                                    <i class="fas fa-map"></i>
                                </button>
                                <button class="btn btn-sm btn-warning btn-edit" title="Edit"
                                        data-id="{{ $item->id }}"
                                        data-val-nama="{{ $item->nama_lokasi }}"
                                        data-val-lat="{{ $item->latitude }}"
                                        data-val-lng="{{ $item->longitude }}"
                                        data-val-radius="{{ $item->radius }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-hapus" title="Hapus"
                                        data-id="{{ $item->id }}"
                                        data-val-nama="{{ $item->nama_lokasi }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyState">
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-map fa-3x mb-3" style="opacity:0.3;"></i>
                                    <p class="mb-0 fw-medium">Belum ada lokasi kantor</p>
                                    <small>Klik "Tambah Lokasi" untuk memulai</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="no-results px-4 pb-4" id="noResults">
            <i class="fas fa-search fa-3x text-muted mb-3" style="opacity:0.35;"></i>
            <div class="fw-semibold text-muted">Lokasi tidak ditemukan</div>
        </div>
    </div>
</div>


{{-- ================================================ --}}
{{-- MODAL: TAMBAH                                    --}}
{{-- ================================================ --}}
<div class="modal fade" id="tambahLokasiKantor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-plus" style="color:#1d4ed8;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Tambah Lokasi Kantor</h6>
                        <small class="text-muted" style="font-size:11px;">Buat lokasi kantor baru</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.lokasi-kantor.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Nama Lokasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_lokasi" class="form-control" style="font-size:13px;"
                               placeholder="contoh: Kantor Pusat" required>
                        <small class="text-muted d-block mt-1">Nama unik untuk identifikasi lokasi</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            <i class="fas fa-map-pin me-1 text-danger"></i> Latitude <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="latitude" step="any" class="form-control coord-input"
                               placeholder="contoh: -6.200000" required>
                        <small class="text-muted d-block mt-1">Format: -90 hingga 90 (6 desimal)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            <i class="fas fa-map-pin me-1 text-danger"></i> Longitude <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="longitude" step="any" class="form-control coord-input"
                               placeholder="contoh: 106.816666" required>
                        <small class="text-muted d-block mt-1">Format: -180 hingga 180 (6 desimal)</small>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            <i class="fas fa-ruler me-1 text-warning"></i> Radius Geofence <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="radius" class="form-control" style="font-size:13px;"
                                   placeholder="contoh: 100" min="10" max="5000" required>
                            <span class="input-group-text">meter</span>
                        </div>
                        <small class="text-muted d-block mt-1">Jangkauan area: 10 - 5000 meter</small>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ================================================ --}}
{{-- MODAL: EDIT                                      --}}
{{-- ================================================ --}}
<div class="modal fade" id="editLokasiKantor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#fffbeb;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fef3c7;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-edit" style="color:#d97706;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Edit Lokasi Kantor</h6>
                        <small class="text-muted" style="font-size:11px;" id="editModalSubtitle">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Nama Lokasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_lokasi" id="editNama"
                               class="form-control" style="font-size:13px;" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                <i class="fas fa-map-pin me-1 text-danger"></i> Latitude <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="latitude" id="editLatitude" step="any"
                                   class="form-control coord-input" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                <i class="fas fa-map-pin me-1 text-danger"></i> Longitude <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="longitude" id="editLongitude" step="any"
                                   class="form-control coord-input" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            <i class="fas fa-ruler me-1 text-warning"></i> Radius Geofence <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="radius" id="editRadius" class="form-control"
                                   style="font-size:13px;" min="10" max="50000" required>
                            <span class="input-group-text">meter</span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm px-4 fw-semibold"
                            style="background:#fbbf24;color:#1e293b;border:none;">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- MODAL: LIHAT PETA                                --}}
{{-- ================================================ --}}
<div class="modal fade" id="lihatPetaLokasiKantor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#f0fdf4;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dcfce7;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-map" style="color:#16a34a;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Peta Lokasi</h6>
                        <small class="text-muted" style="font-size:11px;" id="petaSubtitle">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-3">
                <div class="map-container" id="mapContainer">
                    <div class="text-center">
                        <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                        <p class="mb-0 text-muted">Peta akan ditampilkan di sini</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Latitude</small>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" id="petaLatitude" class="form-control" readonly
                                   style="font-size:12px;font-family:monospace;">
                            <button type="button" class="btn-copy" onclick="copyCoord('petaLatitude')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Longitude</small>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" id="petaLongitude" class="form-control" readonly
                                   style="font-size:12px;font-family:monospace;">
                            <button type="button" class="btn-copy" onclick="copyCoord('petaLongitude')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <small class="text-muted d-block mb-1">Radius</small>
                        <input type="text" id="petaRadius" class="form-control" readonly
                               style="font-size:12px;font-family:monospace;">
                    </div>
                </div>

                <div class="alert alert-info alert-sm mt-3" style="font-size:12px;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Info:</strong> Koordinat ini dapat digunakan untuk geofencing absensi mobile.
                    Copy koordinat dengan tombol di samping untuk digunakan di aplikasi lain.
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- MODAL: HAPUS                                     --}}
{{-- ================================================ --}}
<div class="modal fade" id="hapusLokasiKantor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#fef2f2;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-trash" style="color:#dc2626;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Hapus Lokasi Kantor</h6>
                        <small class="text-muted" style="font-size:11px;">Tindakan tidak bisa dibatalkan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="hapusForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body text-center py-4">
                    <p style="font-size:13px;" class="mb-1">Yakin ingin menghapus lokasi kantor:</p>
                    <div class="fw-bold" style="font-size:14px;color:#1e293b;" id="hapusNama">-</div>
                    <p class="text-muted mt-2" style="font-size:12px;">
                        Data akan dihapus permanen dari sistem.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-semibold">
                        <i class="fas fa-trash me-1"></i> Hapus
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

    // ── Search ──
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const rows        = document.querySelectorAll('#lokasiKantorBody tr:not(#emptyState)');
    const noResults   = document.getElementById('noResults');

    function applySearch() {
        const q = searchInput.value.toLowerCase().trim();
        clearBtn.classList.toggle('show', q.length > 0);
        let visible = 0;
        rows.forEach(row => {
            const match = !q || (row.dataset.nama || '').includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        noResults.classList.toggle('show', visible === 0 && q.length > 0);
    }
    searchInput.addEventListener('input', applySearch);
    clearBtn.addEventListener('click', () => { searchInput.value = ''; applySearch(); searchInput.focus(); });
    searchInput.addEventListener('keydown', e => { if (e.key === 'Escape') clearBtn.click(); });

    // ── Modal Edit ──
    const editModal      = new bootstrap.Modal(document.getElementById('editLokasiKantor'));
    const editForm       = document.getElementById('editForm');
    const baseUpdateUrl  = '{{ route('admin.lokasi-kantor.update', ':id') }}';

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const id       = this.dataset.id;
            const nama     = this.dataset.valNama || '';
            const lat      = this.dataset.valLat  || '';
            const lng      = this.dataset.valLng  || '';
            const radius   = this.dataset.valRadius || '';

            editForm.action = baseUpdateUrl.replace(':id', id);
            document.getElementById('editModalSubtitle').textContent = nama;
            document.getElementById('editNama').value      = nama;
            document.getElementById('editLatitude').value  = lat;
            document.getElementById('editLongitude').value = lng;
            document.getElementById('editRadius').value    = radius;

            editModal.show();
        });
    });

    // ── Modal Lihat Peta ──
    const petaModal = new bootstrap.Modal(document.getElementById('lihatPetaLokasiKantor'));

    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function () {
            const nama   = this.dataset.valNama || '';
            const lat    = this.dataset.valLat  || '';
            const lng    = this.dataset.valLng  || '';
            const radius = this.dataset.valRadius || '';

            document.getElementById('petaSubtitle').textContent = nama;
            document.getElementById('petaLatitude').value  = lat;
            document.getElementById('petaLongitude').value = lng;
            document.getElementById('petaRadius').value    = radius + ' m';

            // Update map (jika integrate dengan Google Maps di masa depan)
            updateMap(lat, lng, radius);

            petaModal.show();
        });
    });

    // ── Modal Hapus ──
    const hapusModal    = new bootstrap.Modal(document.getElementById('hapusLokasiKantor'));
    const hapusForm     = document.getElementById('hapusForm');
    const baseHapusUrl  = '{{ route('admin.lokasi-kantor.destroy', ':id') }}';

    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const nama = this.dataset.valNama || '';

            hapusForm.action = baseHapusUrl.replace(':id', id);
            document.getElementById('hapusNama').textContent = nama;

            hapusModal.show();
        });
    });

    // ── Copy Koordinat ──
    window.copyCoord = function(elementId) {
        const input = document.getElementById(elementId);
        input.select();
        document.execCommand('copy');
        showToast('Koordinat disalin ke clipboard', 'success');
    };

    // ── Update Map (siap untuk integrasi Google Maps) ──
    function updateMap(lat, lng, radius) {
        const mapContainer = document.getElementById('mapContainer');
        mapContainer.innerHTML = `
            <div class="text-center">
                <i class="fas fa-map fa-3x mb-3" style="opacity:0.5;color:#059669;"></i>
                <p class="mb-2">
                    <strong>${lat}, ${lng}</strong><br>
                    <small class="text-muted">Radius: ${radius}m</small>
                </p>
                <small class="text-muted">
                    <i class="fas fa-lightbulb me-1"></i>
                    Integrasi Google Maps akan segera ditambahkan
                </small>
            </div>
        `;
    }

    // ── Toast ──
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
