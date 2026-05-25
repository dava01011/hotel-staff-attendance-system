@extends('admin.layouts.app')

@section('title', 'Shift Pattern Karyawan')

@push('styles')
<style>
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
    .clear-search.show { display: block; }

    .employee-info { display: flex; align-items: center; gap: 12px; }
    .employee-details { display: flex; flex-direction: column; }
    .employee-name { font-weight: 600; color: #2d3748; margin-bottom: 2px; }
    .employee-nip { font-size: 12px; color: #718096; }

    .avatar-initial {
        width: 40px; height: 40px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 16px; color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        flex-shrink: 0;
    }
    .profile-img {
        width: 40px; height: 40px;
        border-radius: 50%; object-fit: cover;
        border: 2px solid #e9ecef; flex-shrink: 0;
    }

    .shift-day-badge {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 3px 8px; border-radius: 12px;
        font-size: 11px; font-weight: 600; margin: 2px;
    }
    .shift-day-badge.kerja { background: #d1fae5; color: #065f46; }
    .shift-day-badge.libur { background: #fee2e2; color: #991b1b; }

    .no-pattern-text { font-size: 12px; color: #adb5bd; font-style: italic; }

    .btn-sm { padding: 6px 12px; transition: all 0.2s; }
    .btn-sm:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

    .table tbody tr { transition: background-color 0.2s; }
    .action-dropdown .dropdown-menu { min-width: 200px; }
    .action-dropdown .dropdown-item { font-size: 13px; padding: 8px 16px; }

    .no-results { display: none; text-align: center; padding: 50px 20px; }
    .no-results.show { display: block; }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Shift Pattern Karyawan</h4>
        <small class="text-muted">Kelola jadwal kerja & libur karyawan per minggu</small>
    </div>
</div>

{{-- Search --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="form-control search-input" id="searchInput"
                    placeholder="Cari nama atau NIP karyawan..." autocomplete="off">
                <button class="clear-search" id="clearSearch" title="Hapus pencarian">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="ms-auto text-muted">
                <small>Total: <strong>{{ $karyawan->count() }}</strong> karyawan</small>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="shiftTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Karyawan</th>
                        <th>Default Pattern</th>
                        <th>Override Minggu Ini</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="shiftTableBody">
                    @forelse ($karyawan as $k)
                        @php
                            $defaultPatterns = $k->shiftPatterns->whereNull('minggu_ke');
                            $weeklyOverrides = $k->shiftPatterns->whereNotNull('minggu_ke');
                            $hariOrder = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
                            $labelHari = [
                                'senin' => 'Sen', 'selasa' => 'Sel', 'rabu' => 'Rab',
                                'kamis' => 'Kam', 'jumat' => 'Jum', 'sabtu' => 'Sab', 'minggu' => 'Min',
                            ];
                        @endphp
                        <tr data-nama="{{ strtolower($k->user->nama ?? '') }}"
                            data-nip="{{ strtolower($k->nip ?? '') }}">
                            <td class="ps-4">{{ $loop->iteration }}</td>

                            <td>
                                <div class="employee-info">
                                    @if(!empty($k->foto_profil))
                                        <img src="{{ asset('storage/' . $k->foto_profil) }}"
                                             alt="{{ $k->user->nama ?? '' }}" class="profile-img">
                                    @else
                                        <div class="avatar-initial">
                                            {{ strtoupper(substr($k->user->nama ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="employee-details">
                                        <div class="employee-name">{{ $k->user->nama ?? '-' }}</div>
                                        <div class="employee-nip">NIP: {{ $k->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($defaultPatterns->isNotEmpty())
                                    @foreach($hariOrder as $hari)
                                        @php $p = $defaultPatterns->firstWhere('hari', $hari); @endphp
                                        @if($p)
                                            <span class="shift-day-badge {{ $p->tipe }}" title="{{ $p->shift ? $p->shift->kode . ' (' . substr($p->shift->jam_masuk, 0, 5) . ' - ' . substr($p->shift->jam_pulang, 0, 5) . ')' : '' }}">
                                                {{ $labelHari[$hari] }}
                                                @if($p->tipe === 'kerja' && $p->shift)
                                                    <small style="font-size: 8px; opacity: 0.8; margin-left: 2px;">({{ $p->shift->kode }})</small>
                                                @endif
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="no-pattern-text">
                                        <i class="fas fa-minus-circle me-1"></i>Belum diset
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($weeklyOverrides->isNotEmpty())
                                    @php $mingguList = $weeklyOverrides->pluck('minggu_ke')->unique()->sort(); @endphp
                                    @foreach($mingguList as $minggu)
                                        @php $tahun = $weeklyOverrides->firstWhere('minggu_ke', $minggu)->tahun; @endphp
                                        <span class="badge bg-warning text-dark me-1">
                                            <i class="fas fa-calendar-week me-1"></i>Minggu {{ $minggu }}/{{ $tahun }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted" style="font-size:12px;">
                                        <i class="fas fa-check-circle text-success me-1"></i>Pakai default
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="dropdown action-dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown"
                                        data-bs-auto-close="true"
                                        aria-expanded="false">
                                        <i class="fas fa-cog me-1"></i> Kelola
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.shift-pattern.default.edit', $k->id) }}">
                                                <i class="fas fa-sliders-h me-2 text-primary"></i>
                                                Edit Default Pattern
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.shift-pattern.weekly.edit', $k->id) }}">
                                                <i class="fas fa-calendar-week me-2 text-warning"></i>
                                                Override Mingguan
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.shift-pattern.calendar', $k->id) }}">
                                                <i class="fas fa-calendar-alt me-2 text-success"></i>
                                                Lihat Kalender
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyState">
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3" style="opacity:0.3;"></i>
                                    <p class="mb-0 fw-medium">Belum ada data karyawan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="no-results px-4 pb-4" id="noResults">
            <i class="fas fa-search fa-3x text-muted mb-3" style="opacity:0.35;"></i>
            <div class="fw-semibold text-muted">Karyawan tidak ditemukan</div>
            <small class="text-muted">Coba ubah kata kunci pencarian</small>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Fix dropdown terpotong oleh overflow container tabel
    // Pakai popperConfig strategy: fixed agar dropdown render relatif ke viewport
    document.querySelectorAll('.action-dropdown [data-bs-toggle="dropdown"]').forEach(btn => {
        new bootstrap.Dropdown(btn, {
            popperConfig: {
                strategy: 'fixed',
                modifiers: [
                    { name: 'preventOverflow', options: { boundary: 'viewport' } },
                ],
            },
        });
    });

    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const rows        = document.querySelectorAll('#shiftTableBody tr:not(#emptyState)');
    const noResults   = document.getElementById('noResults');

    function applySearch() {
        const q = searchInput.value.toLowerCase().trim();
        clearBtn.classList.toggle('show', q.length > 0);
        let visible = 0;
        rows.forEach(row => {
            const match = !q || row.dataset.nama.includes(q) || row.dataset.nip.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        noResults.classList.toggle('show', visible === 0 && q.length > 0);
    }

    searchInput.addEventListener('input', applySearch);
    clearBtn.addEventListener('click', () => { searchInput.value = ''; applySearch(); searchInput.focus(); });
    searchInput.addEventListener('keydown', e => { if (e.key === 'Escape') clearBtn.click(); });

    function showToast(message, type = 'success') {
        const colors = { success: '#28a745', error: '#dc3545' };
        const icons  = { success: 'check-circle', error: 'exclamation-circle' };
        const toast  = document.createElement('div');
        toast.style.cssText = `
            position:fixed;top:20px;right:20px;padding:15px 25px;
            background:${colors[type]};color:white;border-radius:8px;
            font-weight:600;font-size:14px;z-index:9999;
            box-shadow:0 4px 12px rgba(0,0,0,0.15);
            animation:slideInRight 0.3s ease-out;
            display:flex;align-items:center;gap:10px;max-width:420px;
        `;
        toast.innerHTML = `<i class="fas fa-${icons[type]}"></i><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    @if(session('success'))
        showToast(`{!! session('success') !!}`, 'success');
    @endif
    @if(session('error'))
        showToast(`{!! session('error') !!}`, 'error');
    @endif
});
</script>
@endpush
