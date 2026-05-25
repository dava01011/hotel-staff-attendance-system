@extends('admin.layouts.app')

@section('title', 'Data User')

@push('styles')
<style>
    /* ── Search Box ─────────────────────────────────────────── */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .search-input {
        padding-left: 45px;
        border-radius: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
    }

    .clear-search {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 5px;
        display: none;
        transition: color 0.2s;
    }

    .clear-search:hover { color: #dc3545; }
    .clear-search.show  { display: block; }

    /* ── Table ──────────────────────────────────────────────── */
    .table-compact th {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 11px 14px;
        color: #495057;
    }

    .table-compact td { padding: 10px 14px; vertical-align: middle; font-size: 13px; }

    .table tbody tr { transition: background-color 0.15s; }

    .table tbody tr.highlight {
        background-color: #fff3cd !important;
        animation: highlight-fade 1.5s ease-out forwards;
    }

    @keyframes highlight-fade {
        from { background-color: #fff3cd; }
        to   { background-color: transparent; }
    }

    /* ── Employee Cell (User) ───────────────────────────────── */
    .user-compact { display: flex; align-items: center; gap: 10px; }

    .avatar-small {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        color: white;
        flex-shrink: 0;
    }

    .user-name { font-weight: 600; color: #2d3748; line-height: 1.2; }
    .user-email { font-size: 11px; color: #94a3b8; margin-top: 1px; }

    /* ── Status Badge ───────────────────────────────────────── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge i     { font-size: 9px; }
    .status-badge.aktif { background: #d1fae5; color: #065f46; }
    .status-badge.nonaktif { background: #fee2e2; color: #991b1b; }

    /* ── Role Badge ─────────────────────────────────────────── */
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 11px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 700;
    }

    .role-badge i { font-size: 9px; margin-right: 5px; }
    .role-badge.super_admin { background: #fee2e2; color: #991b1b; }
    .role-badge.admin       { background: #dbeafe; color: #1e40af; }
    .role-badge.manager     { background: #e0e7ff; color: #3730a3; }
    .role-badge.gm          { background: #d1fae5; color: #065f46; }
    .role-badge.karyawan    { background: #f1f5f9; color: #475569; }

    /* ── Action Buttons ─────────────────────────────────────── */
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.12); }

    .action-btn.edit   { background: #fff3cd; color: #d97706; }
    .action-btn.edit:hover   { background: #fbbf24; color: white; }

    .action-btn.delete { background: #fee2e2; color: #dc2626; }
    .action-btn.delete:hover { background: #ef4444; color: white; }

    /* ── No Results / Search Stats ──────────────────────────── */
    .no-results      { display: none; text-align: center; padding: 60px 20px; }
    .no-results.show { display: block; }
    .no-results-icon    { font-size: 56px; color: #dee2e6; margin-bottom: 16px; }
    .no-results-text    { color: #6c757d; font-size: 15px; font-weight: 600; margin-bottom: 6px; }
    .no-results-subtext { color: #adb5bd; font-size: 13px; }

    .search-stats {
        font-size: 14px;
        color: #6c757d;
        display: none;
        padding: 12px 0;
        border-top: 1px solid #e9ecef;
        margin-top: 14px;
    }

    .search-stats.show   { display: flex; justify-content: space-between; align-items: center; }
    .search-stats strong { color: #0d6efd; }

    .reset-filter       { font-size: 13px; color: #dc3545; text-decoration: none; font-weight: 600; }
    .reset-filter:hover { text-decoration: underline; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .search-container { max-width: 100%; margin-bottom: 12px; }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">Data User</h4>
        <small class="text-muted">Manajemen akun pengguna</small>
    </div>

    <button class="btn btn-primary btn-sm d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#tambahUser">
        <i class="fas fa-plus"></i> Tambah Data
    </button>
</div>

{{-- Search & Filter Card --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text"
                       class="form-control search-input"
                       id="searchInput"
                       placeholder="Cari nama, email, atau role..."
                       autocomplete="off">
                <button class="clear-search" id="clearSearch" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="ms-auto text-muted">
                <small>Total: <strong>{{ $user->count() }}</strong> user</small>
            </div>
        </div>

        {{-- Search Stats --}}
        <div class="search-stats" id="searchStats">
            <div>
                Menampilkan <strong id="resultCount">0</strong>
                dari <strong>{{ $user->count() }}</strong> user
            </div>
            <a href="#" class="reset-filter" id="resetFilter">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-compact align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:44px; padding-left:20px;">#</th>
                        <th style="min-width:200px;">User</th>
                        <th style="min-width:170px;">Email</th>
                        <th style="width:170px;">Role</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:100px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @php
                        $colors  = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4'];
                    @endphp
                    @forelse ($user as $item)
                        @php
                            $nama  = $item->nama;
                            $ci    = ord(strtolower(substr($nama, 0, 1))) % count($colors);
                        @endphp
                        <tr data-nama="{{ strtolower($nama) }}"
                            data-email="{{ strtolower($item->email) }}"
                            data-role="{{ strtolower($item->role) }}"
                            data-status="{{ strtolower($item->status) }}">

                            <td style="padding-left:20px; color:#94a3b8; font-size:12px;">
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <div class="user-compact">
                                    <div class="avatar-small" style="background:{{ $colors[$ci] }};">
                                        {{ strtoupper(substr($nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="user-name">{{ $nama }}</div>
                                        <div class="user-email">{{ $item->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $item->email }}</td>

                            <td>
                                @php
                                    $roleIcon = match($item->role) {
                                        'super_admin' => 'crown',
                                        'admin'       => 'user-shield',
                                        'manager'     => 'user-graduate',
                                        'gm'          => 'user-astronaut',
                                        'karyawan'    => 'user',
                                        default       => 'user',
                                    };
                                @endphp
                                <span class="role-badge {{ $item->role }}">
                                    <i class="fas fa-{{ $roleIcon }}"></i>
                                    {{ ucfirst(str_replace('_', ' ', $item->role)) }}
                                </span>
                            </td>

                            <td>
                                <span class="status-badge {{ $item->status == 'aktif' ? 'aktif' : 'nonaktif' }}">
                                    <i class="fas fa-circle"></i>
                                    {{ $item->status == 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td style="text-align:center;">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="action-btn edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#ubahUser{{ $item->id }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#hapusUser{{ $item->id }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyState">
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3" style="opacity:.2;"></i>
                                    <p class="mb-0 fw-medium">Belum ada data user</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- No Results --}}
        <div class="no-results" id="noResults">
            <div class="no-results-icon"><i class="fas fa-search"></i></div>
            <div class="no-results-text">Tidak ada user ditemukan</div>
            <div class="no-results-subtext">Coba ubah kata kunci pencarian</div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('admin.user.create')
@foreach ($user as $item)
    @include('admin.user.edit')
    @include('admin.user.delete')
@endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const resetFilter = document.getElementById('resetFilter');
    const tableBody   = document.getElementById('userTableBody');
    const noResults   = document.getElementById('noResults');
    const searchStats = document.getElementById('searchStats');
    const resultCount = document.getElementById('resultCount');
    const allRows     = tableBody.querySelectorAll('tr:not(#emptyState)');

    let currentSearch = '';

    function applySearch() {
        const term = currentSearch;
        let visible = 0;

        allRows.forEach(row => {
            const nama   = row.dataset.nama;
            const email  = row.dataset.email;
            const role   = row.dataset.role;
            const status = row.dataset.status;

            const match = !term || nama.includes(term) || email.includes(term) || role.includes(term) || status.includes(term);

            if (match) {
                row.style.display = '';
                visible++;
                if (term) {
                    row.classList.add('highlight');
                    setTimeout(() => row.classList.remove('highlight'), 1500);
                }
            } else {
                row.style.display = 'none';
            }
        });

        resultCount.textContent = visible;
        const active = term.length > 0;
        searchStats.classList.toggle('show', active);
        clearBtn.classList.toggle('show', active);
        noResults.classList.toggle('show', visible === 0 && active);
    }

    searchInput.addEventListener('input', function() {
        currentSearch = this.value.toLowerCase().trim();
        applySearch();
    });

    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        currentSearch = '';
        clearBtn.classList.remove('show');
        applySearch();
        searchInput.focus();
    });

    resetFilter.addEventListener('click', function(e) {
        e.preventDefault();
        clearBtn.click();
    });

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') clearBtn.click();
    });

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });

    // Password toggle di modal
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `
            position:fixed; top:20px; right:20px;
            padding:13px 20px;
            background:${type === 'success' ? '#16a34a' : '#dc2626'};
            color:white; border-radius:10px; font-weight:600; font-size:13px;
            z-index:9999; box-shadow:0 4px 14px rgba(0,0,0,.15);
            display:flex; align-items:center; gap:10px;
            animation: slideInR .3s ease-out;
        `;
        t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span>`;
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 3000);
    }

    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ str_replace("'", "\'", $error) }}', 'error');
        @endforeach
    @endif
});
</script>

<style>
@keyframes slideInR {
    from { transform: translateX(80px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
</style>
@endpush