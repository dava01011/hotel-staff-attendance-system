@extends('admin.layouts.app')

@section('title', 'Jenis Cuti')

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

    .feature-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 20px;
        font-size: 11px; font-weight: 600; margin: 2px;
    }
    .feature-badge.yes { background: #d1fae5; color: #065f46; }
    .feature-badge.no  { background: #f1f5f9; color: #94a3b8; }

    .status-toggle {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
        cursor: pointer; border: none; transition: all 0.2s;
        text-decoration: none;
    }
    .status-toggle.aktif    { background: #d1fae5; color: #065f46; }
    .status-toggle.nonaktif { background: #fee2e2; color: #991b1b; }
    .status-toggle:hover { opacity: 0.8; transform: translateY(-1px); }

    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .status-badge.aktif    { background: #d1fae5; color: #065f46; }
    .status-badge.nonaktif { background: #fee2e2; color: #991b1b; }

    .btn-sm { padding: 6px 12px; transition: all 0.2s; }
    .btn-sm:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .table tbody tr { transition: background-color 0.2s; }
    .table tbody tr.nonaktif-row { opacity: 0.6; }

    .no-results { display: none; text-align: center; padding: 50px 20px; }
    .no-results.show { display: block; }

    .toggle-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; border-radius: 10px; background: #f8fafc;
        margin-bottom: 10px;
    }
    .toggle-row .toggle-label { font-size: 13px; font-weight: 600; color: #374151; }
    .toggle-row .toggle-desc  { font-size: 11px; color: #9ca3af; margin-top: 1px; }
    .form-check-input { width: 2.5em; height: 1.3em; cursor: pointer; }
    .form-check-input:checked { background-color: #0d6efd; border-color: #0d6efd; }

    .alert-soft-info {
        background: #e6f2ff;
        border-left: 4px solid #0d6efd;
        color: #084298;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 13px;
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
        <h4 class="fw-bold mb-1">Jenis Cuti</h4>
        <small class="text-muted">Kelola jenis-jenis cuti karyawan</small>
    </div>
    @if($canCRUD)
        <button class="btn btn-sm btn-primary d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#tambahJenisCuti">
            <i class="fas fa-plus"></i> Tambah Jenis Cuti
        </button>
    @endif
</div>

{{-- Info untuk role non-CRUD (admin, manager, gm) --}}
@if(!$canCRUD && in_array(Auth::user()->role, ['admin', 'manager', 'gm']))
    <div class="alert-soft-info mb-3">
        <i class="fas fa-info-circle me-2"></i>
        Anda hanya dapat melihat data jenis cuti. Untuk mengubah data, silakan hubungi HRD atau Super Admin.
    </div>
@endif

{{-- Search & Stats --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="form-control search-input" id="searchInput"
                    placeholder="Cari nama jenis cuti..." autocomplete="off">
                <button class="clear-search" id="clearSearch">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="ms-auto d-flex gap-2">
                <span class="badge bg-success px-3 py-2" style="font-size:12px;">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ $jenisCuti->where('aktif', true)->count() }} aktif
                </span>
                <span class="badge bg-secondary px-3 py-2" style="font-size:12px;">
                    <i class="fas fa-times-circle me-1"></i>
                    {{ $jenisCuti->where('aktif', false)->count() }} nonaktif
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
                        <th>Nama Jenis Cuti</th>
                        <th>Deskripsi</th>
                        <th>Butuh File</th>
                        <th>Potong Jatah</th>
                        <th>Status</th>
                        @if($canCRUD)
                            <th class="text-center pe-4">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="jenisCutiBody">
                    @forelse($jenisCuti as $item)
                        <tr class="{{ !$item->aktif ? 'nonaktif-row' : '' }}"
                            data-nama="{{ strtolower($item->nama) }}">
                            <td class="ps-4">{{ $loop->iteration }}</td>

                            <td>
                                <div class="fw-semibold" style="color:#2d3748;font-size:14px;">
                                    {{ $item->nama }}
                                </div>
                            </td>

                            <td>
                                <span class="text-muted" style="font-size:13px;">
                                    {{ $item->deskripsi ? \Illuminate\Support\Str::limit($item->deskripsi, 50) : '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="feature-badge {{ $item->butuh_file ? 'yes' : 'no' }}">
                                    @if($item->butuh_file)
                                        <i class="fas fa-paperclip"></i> Diperlukan
                                    @else
                                        <i class="fas fa-minus"></i> Tidak
                                    @endif
                                </span>
                            </td>

                            <td>
                                <span class="feature-badge {{ $item->potong_jatah ? 'yes' : 'no' }}">
                                    @if($item->potong_jatah)
                                        <i class="fas fa-scissors"></i> Dipotong
                                    @else
                                        <i class="fas fa-gift"></i> Tidak
                                    @endif
                                </span>
                            </td>

                            <td>
                                @if($canCRUD)
                                    <form action="{{ route('admin.jenis-cuti.toggle', $item->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="status-toggle {{ $item->aktif ? 'aktif' : 'nonaktif' }}"
                                                title="Klik untuk {{ $item->aktif ? 'nonaktifkan' : 'aktifkan' }}">
                                            <i class="fas fa-circle" style="font-size:8px;"></i>
                                            {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="status-badge {{ $item->aktif ? 'aktif' : 'nonaktif' }}">
                                        <i class="fas fa-circle" style="font-size:8px;"></i>
                                        {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>

                            @if($canCRUD)
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-warning btn-edit" title="Edit"
                                            data-id="{{ $item->id }}"
                                            data-val-nama="{{ $item->nama }}"
                                            data-val-deskripsi="{{ $item->deskripsi }}"
                                            data-val-butuh-file="{{ $item->butuh_file ? '1' : '0' }}"
                                            data-val-potong-jatah="{{ $item->potong_jatah ? '1' : '0' }}"
                                            data-val-aktif="{{ $item->aktif ? '1' : '0' }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-hapus" title="Hapus"
                                            data-id="{{ $item->id }}"
                                            data-val-nama="{{ $item->nama }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr id="emptyState">
                            <td @if($canCRUD) colspan="7" @else colspan="6" @endif class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3" style="opacity:0.3;"></i>
                                    <p class="mb-0 fw-medium">Belum ada jenis cuti</p>
                                    <small>Klik "Tambah Jenis Cuti" untuk memulai</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="no-results px-4 pb-4" id="noResults">
            <i class="fas fa-search fa-3x text-muted mb-3" style="opacity:0.35;"></i>
            <div class="fw-semibold text-muted">Jenis cuti tidak ditemukan</div>
        </div>
    </div>
</div>

{{-- Modals hanya ditampilkan jika canCRUD --}}
@if($canCRUD)
    {{-- Modal Tambah --}}
    <div class="modal fade" id="tambahJenisCuti" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-plus" style="color:#1d4ed8;font-size:14px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size:14px;">Tambah Jenis Cuti</h6>
                            <small class="text-muted" style="font-size:11px;">Buat jenis cuti baru</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.jenis-cuti.store') }}" method="POST">
                    @csrf
                    <div class="modal-body pt-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Nama Jenis Cuti <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" class="form-control" style="font-size:13px;"
                                   placeholder="contoh: Cuti Tahunan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" style="font-size:13px;" rows="2"
                                      placeholder="Penjelasan singkat tentang jenis cuti ini"></textarea>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold" style="font-size:13px;">Pengaturan</label>
                            <div class="toggle-row">
                                <div>
                                    <div class="toggle-label">
                                        <i class="fas fa-paperclip me-1 text-primary"></i> Butuh File Lampiran
                                    </div>
                                    <div class="toggle-desc">Karyawan wajib upload dokumen saat mengajukan</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="butuh_file" value="1" id="tambahButuhFile">
                                </div>
                            </div>
                            <div class="toggle-row">
                                <div>
                                    <div class="toggle-label">
                                        <i class="fas fa-scissors me-1 text-danger"></i> Potong Jatah Cuti
                                    </div>
                                    <div class="toggle-desc">Cuti ini akan mengurangi saldo jatah cuti karyawan</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="potong_jatah" value="1" id="tambahPotongJatah" checked>
                                </div>
                            </div>
                            <div class="toggle-row mb-0">
                                <div>
                                    <div class="toggle-label">
                                        <i class="fas fa-toggle-on me-1 text-success"></i> Aktif
                                    </div>
                                    <div class="toggle-desc">Jenis cuti ini tersedia untuk digunakan karyawan</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="aktif" value="1" id="tambahAktif" checked>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editJenisCuti" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0 pb-0" style="background:#fffbeb;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fef3c7;
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-edit" style="color:#d97706;font-size:14px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size:14px;">Edit Jenis Cuti</h6>
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
                                Nama Jenis Cuti <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" id="editNama"
                                   class="form-control" style="font-size:13px;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">Deskripsi</label>
                            <textarea name="deskripsi" id="editDeskripsi"
                                      class="form-control" style="font-size:13px;" rows="2"></textarea>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold" style="font-size:13px;">Pengaturan</label>
                            <div class="toggle-row">
                                <div>
                                    <div class="toggle-label">
                                        <i class="fas fa-paperclip me-1 text-primary"></i> Butuh File Lampiran
                                    </div>
                                    <div class="toggle-desc">Karyawan wajib upload dokumen saat mengajukan</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="editButuhFile">
                                </div>
                            </div>
                            <div class="toggle-row">
                                <div>
                                    <div class="toggle-label">
                                        <i class="fas fa-scissors me-1 text-danger"></i> Potong Jatah Cuti
                                    </div>
                                    <div class="toggle-desc">Cuti ini akan mengurangi saldo jatah cuti karyawan</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="editPotongJatah">
                                </div>
                            </div>
                            <div class="toggle-row mb-0">
                                <div>
                                    <div class="toggle-label">
                                        <i class="fas fa-toggle-on me-1 text-success"></i> Aktif
                                    </div>
                                    <div class="toggle-desc">Jenis cuti ini tersedia untuk digunakan karyawan</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="editAktif">
                                </div>
                            </div>
                            <input type="hidden" name="butuh_file"   id="editButuhFileVal"   value="0">
                            <input type="hidden" name="potong_jatah" id="editPotongJatahVal" value="0">
                            <input type="hidden" name="aktif"        id="editAktifVal"       value="0">
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

    {{-- Modal Hapus --}}
    <div class="modal fade" id="hapusJenisCuti" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0 pb-0" style="background:#fef2f2;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-trash" style="color:#dc2626;font-size:14px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size:14px;">Hapus Jenis Cuti</h6>
                            <small class="text-muted" style="font-size:11px;">Tindakan tidak bisa dibatalkan</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="hapusForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <p style="font-size:13px;" class="mb-1">Yakin ingin menghapus jenis cuti:</p>
                        <div class="fw-bold" style="font-size:14px;color:#1e293b;" id="hapusNama">-</div>
                        <p class="text-muted mt-2" style="font-size:12px;">
                            Data akan dihapus permanen. Pastikan jenis cuti ini<br>tidak sedang digunakan karyawan.
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
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const rows        = document.querySelectorAll('#jenisCutiBody tr:not(#emptyState)');
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

    @if($canCRUD)
        // Edit Modal
        const editModal      = new bootstrap.Modal(document.getElementById('editJenisCuti'));
        const editForm       = document.getElementById('editForm');
        const baseUpdateUrl  = '{{ route('admin.jenis-cuti.update', ':id') }}';

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function () {
                const id          = this.dataset.id;
                const nama        = this.dataset.valNama        || '';
                const deskripsi   = this.dataset.valDeskripsi   || '';
                const butuhFile   = this.dataset.valButuhFile   === '1';
                const potongJatah = this.dataset.valPotongJatah === '1';
                const aktif       = this.dataset.valAktif       === '1';

                editForm.action = baseUpdateUrl.replace(':id', id);
                document.getElementById('editModalSubtitle').textContent = nama;
                document.getElementById('editNama').value                = nama;
                document.getElementById('editDeskripsi').value           = deskripsi || '';
                document.getElementById('editButuhFile').checked         = butuhFile;
                document.getElementById('editPotongJatah').checked       = potongJatah;
                document.getElementById('editAktif').checked             = aktif;

                document.getElementById('editButuhFileVal').value   = butuhFile   ? '1' : '0';
                document.getElementById('editPotongJatahVal').value = potongJatah ? '1' : '0';
                document.getElementById('editAktifVal').value       = aktif       ? '1' : '0';

                editModal.show();
            });
        });

        editForm.addEventListener('submit', function () {
            document.getElementById('editButuhFileVal').value   = document.getElementById('editButuhFile').checked   ? '1' : '0';
            document.getElementById('editPotongJatahVal').value = document.getElementById('editPotongJatah').checked ? '1' : '0';
            document.getElementById('editAktifVal').value       = document.getElementById('editAktif').checked       ? '1' : '0';
        });

        // Delete Modal
        const hapusModal    = new bootstrap.Modal(document.getElementById('hapusJenisCuti'));
        const hapusForm     = document.getElementById('hapusForm');
        const baseHapusUrl  = '{{ route('admin.jenis-cuti.destroy', ':id') }}';

        document.querySelectorAll('.btn-hapus').forEach(btn => {
            btn.addEventListener('click', function () {
                const id   = this.dataset.id;
                const nama = this.dataset.valNama || '';

                hapusForm.action = baseHapusUrl.replace(':id', id);
                document.getElementById('hapusNama').textContent = nama;

                hapusModal.show();
            });
        });
    @endif

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