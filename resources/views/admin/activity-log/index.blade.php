@extends('admin.layouts.app')

@section('title', 'Activity Log')

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

    /* ── Filter Pills ───────────────────────────────────────── */
    .filter-group { display: flex; gap: 8px; flex-wrap: wrap; }

    .filter-btn {
        padding: 6px 16px;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: inherit;
    }

    .filter-btn:hover         { border-color: #0d6efd; background: #f8f9fa; color: inherit; }
    .filter-btn.active        { background: #0d6efd; color: white; border-color: #0d6efd; }
    .filter-btn .count        { background: rgba(0,0,0,.1); padding: 2px 8px; border-radius: 10px; font-size: 11px; }
    .filter-btn.active .count { background: rgba(255,255,255,.2); }

    /* ── Search Stats ───────────────────────────────────────── */
    .search-stats {
        font-size: 14px;
        color: #6c757d;
        display: none;
        padding: 12px 0;
        border-top: 1px solid #e9ecef;
        margin-top: 15px;
    }

    .search-stats.show   { display: flex; justify-content: space-between; align-items: center; }
    .search-stats strong { color: #0d6efd; }

    .reset-filter       { font-size: 13px; color: #dc3545; text-decoration: none; font-weight: 600; }
    .reset-filter:hover { text-decoration: underline; }

    /* ── Stats Cards ────────────────────────────────────────── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border: 1px solid #eaecf0;
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: box-shadow 0.2s;
    }

    .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-icon-wrap.total  { background: #eff6ff; color: #3b82f6; }
    .stat-icon-wrap.create { background: #f0fdf4; color: #16a34a; }
    .stat-icon-wrap.update { background: #fff7ed; color: #ea580c; }
    .stat-icon-wrap.delete { background: #fef2f2; color: #dc2626; }

    .stat-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; margin-bottom: 2px; }
    .stat-value { font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1; }

    /* ── Advanced Filter ────────────────────────────────────── */
    .adv-filter-wrap {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
    }

    #advancedFilter {
        margin-top: 14px;
    }

    .filter-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 5px;
        display: block;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        padding: 8px 12px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 13px;
        color: #334155;
        transition: border-color 0.2s;
        background: white;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,.1);
    }

    .toggle-chevron {
        font-size: 10px;
        transition: transform 0.25s ease;
        display: inline-block;
    }

    .toggle-chevron.open { transform: rotate(180deg); }

    /* ── Delete Button ──────────────────────────────────────── */
    .btn-delete-menu {
        position: relative;
        display: inline-block;
    }

    .btn-delete-main {
        background: #dc2626;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-delete-main:hover { background: #b91c1c; box-shadow: 0 4px 12px rgba(220,38,38,.3); }

    /* ── Log Feed ───────────────────────────────────────────── */
    .log-feed { display: flex; flex-direction: column; }

    .log-item {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 16px 6px;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s, padding-left 0.15s;
        border-radius: 0;
    }

    .log-item:last-child  { border-bottom: none; }
    .log-item:hover       { background: #f8fafc; padding-left: 12px; border-radius: 8px; }

    .log-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .log-icon.create  { background: #dcfce7; color: #16a34a; }
    .log-icon.update  { background: #fff7ed; color: #ea580c; }
    .log-icon.delete  { background: #fee2e2; color: #dc2626; }
    .log-icon.approve { background: #dbeafe; color: #2563eb; }
    .log-icon.reject  { background: #fef9c3; color: #d97706; }
    .log-icon.default { background: #f1f5f9; color: #64748b; }

    .log-content { flex: 1; min-width: 0; }

    .log-top {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 10px;
        margin-bottom: 4px;
    }

    .log-user  { font-weight: 700; font-size: 14px; color: #1e293b; }
    .log-time  { font-size: 12px; color: #94a3b8; white-space: nowrap; flex-shrink: 0; }

    .log-desc {
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
        margin-bottom: 8px;
    }

    .log-meta { display: flex; flex-wrap: wrap; gap: 6px; }

    .meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .meta-badge.module { background: #f0fdfa; color: #0f766e; }
    .meta-badge.action { background: #fff7ed; color: #c2410c; }
    .meta-badge.role   { background: #f5f3ff; color: #7c3aed; }
    .meta-badge.ip     { background: #f1f5f9; color: #475569; }

    /* ── Empty State ────────────────────────────────────────── */
    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state i { font-size: 56px; color: #dee2e6; margin-bottom: 16px; display: block; }
    .empty-state p { font-size: 15px; color: #6c757d; font-weight: 600; margin-bottom: 4px; }
    .empty-state small { color: #adb5bd; }

    /* ── Delete Modal ───────────────────────────────────────── */
    .delete-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .delete-modal-overlay.show { display: flex; }

    .delete-modal-content {
        background: white;
        width: 500px;
        max-width: 90%;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,.3);
        overflow: hidden;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from { transform: translateY(40px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }

    .delete-modal-header {
        padding: 24px;
        background: #fee2e2;
        border-bottom: 1px solid #fecaca;
    }

    .delete-modal-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #dc2626;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 12px;
    }

    .delete-modal-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #b91c1c;
        margin: 0 0 4px 0;
        text-align: center;
    }

    .delete-modal-header small {
        display: block;
        text-align: center;
        color: #991b1b;
        font-size: 13px;
    }

    .delete-modal-body {
        padding: 20px 24px;
    }

    .delete-form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }

    .delete-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .delete-modal-body label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 5px;
    }

    .delete-modal-body input,
    .delete-modal-body select {
        width: 100%;
        padding: 8px 12px;
        border: 2px solid #fee2e2;
        border-radius: 8px;
        font-size: 13px;
        color: #334155;
    }

    .delete-modal-body input:focus,
    .delete-modal-body select:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 0.15rem rgba(220,38,38,.1);
    }

    .delete-warning {
        background: #fef2f2;
        border-left: 4px solid #dc2626;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 12px;
        color: #991b1b;
        margin-bottom: 14px;
    }

    .delete-count-preview {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        font-size: 13px;
        color: #475569;
        margin-bottom: 14px;
        display: none;
    }

    .delete-count-preview.show { display: block; }

    .delete-count-preview strong {
        font-size: 24px;
        color: #dc2626;
        display: block;
        margin-bottom: 4px;
    }

    .delete-modal-footer {
        padding: 14px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        background: #f8fafc;
    }

    .btn-cancel {
        background: #e2e8f0;
        color: #475569;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel:hover { background: #cbd5e1; }

    .btn-confirm-delete {
        background: #dc2626;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-confirm-delete:hover { background: #b91c1c; box-shadow: 0 4px 12px rgba(220,38,38,.3); }
    .btn-confirm-delete:disabled { background: #cbd5e1; cursor: not-allowed; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }

    @media (max-width: 992px) {
        .search-container { max-width: 100%; margin-bottom: 15px; }
        .filter-group     { margin-top: 15px; }
        .search-stats     { flex-direction: column; gap: 10px; align-items: flex-start; }
    }

    @media (max-width: 640px) {
        .stats-grid             { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .log-top                { flex-direction: column; gap: 2px; }
        .filter-form-grid       { grid-template-columns: 1fr; }
        .delete-form-row        { grid-template-columns: 1fr; }
        .delete-modal-content   { width: 95%; }
    }
</style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Activity Log</h4>
            <small class="text-muted">Riwayat aktivitas pengguna di sistem</small>
        </div>
        <button type="button" class="btn-delete-main" onclick="showDeleteModal('date')">
            <i class="fas fa-trash"></i> Hapus Log
        </button>
    </div>

    {{-- ── Stats Cards ──────────────────────────────────────── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap total"><i class="fas fa-list"></i></div>
            <div>
                <div class="stat-label">Total Log</div>
                <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap create"><i class="fas fa-plus-circle"></i></div>
            <div>
                <div class="stat-label">Create</div>
                <div class="stat-value">{{ $stats['create'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap update"><i class="fas fa-edit"></i></div>
            <div>
                <div class="stat-label">Update</div>
                <div class="stat-value">{{ $stats['update'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap delete"><i class="fas fa-trash"></i></div>
            <div>
                <div class="stat-label">Delete</div>
                <div class="stat-value">{{ $stats['delete'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- ── Search + Filter Card ─────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            {{-- Search bar --}}
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           class="form-control search-input"
                           id="searchInput"
                           placeholder="Cari nama user atau deskripsi aktivitas..."
                           autocomplete="off">
                    <button class="clear-search" id="clearSearch" title="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="ms-auto text-muted">
                    <small>Halaman ini: <strong>{{ $logs->count() }}</strong> log</small>
                </div>
            </div>

            {{-- Action Filter Pills --}}
            @php
                $currentAction = request('action');
                $baseQuery     = request()->except('action', 'page');
            @endphp

            <div class="filter-group mt-3">
                <a href="{{ route('admin.activity-log.index', $baseQuery) }}"
                   class="filter-btn {{ !$currentAction ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i> Semua
                    <span class="count">{{ $stats['total'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.activity-log.index', array_merge($baseQuery, ['action' => 'create'])) }}"
                   class="filter-btn {{ $currentAction == 'create' ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i> Create
                    <span class="count">{{ $stats['create'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.activity-log.index', array_merge($baseQuery, ['action' => 'update'])) }}"
                   class="filter-btn {{ $currentAction == 'update' ? 'active' : '' }}">
                    <i class="fas fa-edit"></i> Update
                    <span class="count">{{ $stats['update'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.activity-log.index', array_merge($baseQuery, ['action' => 'delete'])) }}"
                   class="filter-btn {{ $currentAction == 'delete' ? 'active' : '' }}">
                    <i class="fas fa-trash"></i> Delete
                    <span class="count">{{ $stats['delete'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.activity-log.index', array_merge($baseQuery, ['action' => 'approve'])) }}"
                   class="filter-btn {{ $currentAction == 'approve' ? 'active' : '' }}">
                    <i class="fas fa-check-circle"></i> Approve
                    <span class="count">{{ $stats['approve'] ?? 0 }}</span>
                </a>
                <a href="{{ route('admin.activity-log.index', array_merge($baseQuery, ['action' => 'reject'])) }}"
                   class="filter-btn {{ $currentAction == 'reject' ? 'active' : '' }}">
                    <i class="fas fa-times-circle"></i> Reject
                    <span class="count">{{ $stats['reject'] ?? 0 }}</span>
                </a>
            </div>

            {{-- Advanced Filter Toggle --}}
            @php $hasAdvFilter = request()->hasAny(['search','role','module','date_from','date_to']); @endphp

            <div class="adv-filter-wrap">
                <button type="button" id="advancedToggle"
                        class="btn btn-link p-0 border-0 shadow-none"
                        style="font-size:13px; color:#64748b; text-decoration:none;">
                    <i class="fas fa-sliders-h me-1"></i>
                    Filter Lanjutan
                    <i class="fas fa-chevron-down ms-1 toggle-chevron {{ $hasAdvFilter ? 'open' : '' }}"></i>
                </button>

                {{-- Panel: hidden by default, shown if filter active --}}
                <div id="advancedFilter" style="{{ $hasAdvFilter ? '' : 'display:none;' }}">
                    <form action="{{ route('admin.activity-log.index') }}" method="GET">
                        @if($currentAction)
                            <input type="hidden" name="action" value="{{ $currentAction }}">
                        @endif
                        <div class="filter-form-grid">
                            <div>
                                <label class="filter-label">Nama User</label>
                                <input type="text" name="search" class="filter-input"
                                       placeholder="Cari nama..." value="{{ request('search') }}">
                            </div>
                            <div>
                                <label class="filter-label">Role</label>
                                <select name="role" class="filter-select">
                                    <option value="">Semua Role</option>
                                    <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="admin"       {{ request('role') == 'admin'       ? 'selected' : '' }}>Admin</option>
                                    <option value="karyawan"    {{ request('role') == 'karyawan'    ? 'selected' : '' }}>Karyawan</option>
                                </select>
                            </div>
                            <div>
                                <label class="filter-label">Module</label>
                                <select name="module" class="filter-select">
                                    <option value="">Semua Module</option>
                                    <option value="cuti"         {{ request('module') == 'cuti'         ? 'selected' : '' }}>Cuti</option>
                                    <option value="absensi"      {{ request('module') == 'absensi'      ? 'selected' : '' }}>Absensi</option>
                                    <option value="shift"        {{ request('module') == 'shift'        ? 'selected' : '' }}>Shift</option>
                                    <option value="jadwal_shift" {{ request('module') == 'jadwal_shift' ? 'selected' : '' }}>Jadwal Shift</option>
                                    <option value="karyawan"     {{ request('module') == 'karyawan'     ? 'selected' : '' }}>Karyawan</option>
                                    <option value="user"         {{ request('module') == 'user'         ? 'selected' : '' }}>User</option>
                                </select>
                            </div>
                            <div>
                                <label class="filter-label">Dari Tanggal</label>
                                <input type="date" name="date_from" class="filter-input"
                                       value="{{ request('date_from') }}">
                            </div>
                            <div>
                                <label class="filter-label">Sampai Tanggal</label>
                                <input type="date" name="date_to" class="filter-input"
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search me-1"></i>Terapkan
                            </button>
                            <a href="{{ route('admin.activity-log.index') }}"
                               class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-redo me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Search Stats (client-side) --}}
            <div class="search-stats" id="searchStats">
                <div>
                    Menampilkan <strong id="resultCount">0</strong> dari
                    <strong>{{ $logs->count() }}</strong> log di halaman ini
                </div>
                <a href="#" class="reset-filter" id="resetSearch">
                    <i class="fas fa-redo"></i> Reset Pencarian
                </a>
            </div>

        </div>
    </div>

    {{-- ── Log Feed ──────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0">
        <div class="card-body" style="padding: 8px 20px;">

            <div class="log-feed" id="logFeed">
                @forelse($logs as $log)
                    @php
                        $actionIcon = match($log->action) {
                            'create'  => 'fa-plus',
                            'update'  => 'fa-edit',
                            'delete'  => 'fa-trash',
                            'approve' => 'fa-check',
                            'reject'  => 'fa-times',
                            default   => 'fa-info',
                        };
                        $iconClass = in_array($log->action, ['create','update','delete','approve','reject'])
                            ? $log->action : 'default';
                    @endphp
                    <div class="log-item"
                         data-user="{{ strtolower($log->user?->nama ?? 'system') }}"
                         data-desc="{{ strtolower($log->description ?? '') }}">

                        <div class="log-icon {{ $iconClass }}">
                            <i class="fas {{ $actionIcon }}"></i>
                        </div>

                        <div class="log-content">
                            <div class="log-top">
                                <div class="log-user">{{ $log->user?->nama ?? 'System' }}</div>
                                <div class="log-time">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <div class="log-desc">{{ $log->description }}</div>

                            <div class="log-meta">
                                <span class="meta-badge module">
                                    <i class="fas fa-cube"></i> {{ ucfirst($log->module) }}
                                </span>
                                <span class="meta-badge action">
                                    <i class="fas fa-bolt"></i> {{ ucfirst($log->action) }}
                                </span>
                                @if($log->role)
                                    <span class="meta-badge role">
                                        <i class="fas fa-user-tag"></i>
                                        {{ ucfirst(str_replace('_', ' ', $log->role)) }}
                                    </span>
                                @endif
                                @if($log->ip_address)
                                    <span class="meta-badge ip">
                                        <i class="fas fa-network-wired"></i> {{ $log->ip_address }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Tidak ada activity log ditemukan</p>
                        <small>Coba ubah filter atau kata kunci pencarian</small>
                    </div>
                @endforelse
            </div>

            {{-- No Results (client-side search) --}}
            <div class="empty-state" id="noResults" style="display:none;">
                <i class="fas fa-search"></i>
                <p>Tidak ada log yang cocok</p>
                <small>Coba ubah kata kunci pencarian</small>
            </div>

        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top d-flex justify-content-center py-3">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- DELETE MODAL --}}
    <div id="deleteModal" class="delete-modal-overlay">
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <div class="delete-modal-icon"><i class="fas fa-trash"></i></div>
                <h3>Hapus Activity Log</h3>
                <small id="deleteModalSubtitle">Pilih kriteria penghapusan log</small>
            </div>

            <form id="deleteForm" method="POST">
                @csrf
                <div class="delete-modal-body">

                    <div class="delete-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>

                    {{-- Delete Type Selection --}}
                    <div class="delete-form-grid" id="deleteTypeGrid">
                        <!-- Will be filled by JavaScript -->
                    </div>

                    {{-- Count Preview --}}
                    <div class="delete-count-preview" id="countPreview">
                        <strong id="countValue">0</strong>
                        <span id="countLabel">log akan dihapus</span>
                    </div>

                </div>

                <div class="delete-modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">
                        Batal
                    </button>
                    <button type="submit" class="btn-confirm-delete" id="btnConfirmDelete">
                        <i class="fas fa-check me-1"></i>Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Advanced Filter Toggle ─────────────────────────────── */
    const advancedToggle = document.getElementById('advancedToggle');
    const advancedFilter = document.getElementById('advancedFilter');
    const chevron        = advancedToggle ? advancedToggle.querySelector('.toggle-chevron') : null;

    if (advancedToggle && advancedFilter) {
        advancedToggle.addEventListener('click', function () {
            const isHidden = advancedFilter.style.display === 'none' || advancedFilter.style.display === '';

            if (isHidden) {
                advancedFilter.style.display = 'block';
                if (chevron) chevron.classList.add('open');
                this.setAttribute('aria-expanded', 'true');
            } else {
                advancedFilter.style.display = 'none';
                if (chevron) chevron.classList.remove('open');
                this.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* ── Client-side search ─────────────────────────────────── */
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const searchStats = document.getElementById('searchStats');
    const resultCount = document.getElementById('resultCount');
    const resetSearch = document.getElementById('resetSearch');
    const noResults   = document.getElementById('noResults');
    const logFeed     = document.getElementById('logFeed');
    const allItems    = logFeed ? logFeed.querySelectorAll('.log-item') : [];

    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        clearBtn.classList.toggle('show', q.length > 0);
        applySearch(q);
    });

    function applySearch(q) {
        let visible = 0;

        allItems.forEach(item => {
            const match = !q ||
                item.getAttribute('data-user').includes(q) ||
                item.getAttribute('data-desc').includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        searchStats.classList.toggle('show', q.length > 0);
        resultCount.textContent = visible;

        const isEmpty = visible === 0 && q.length > 0;
        noResults.style.display = isEmpty ? 'block' : 'none';
        if (logFeed) logFeed.style.display = isEmpty ? 'none' : '';
    }

    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        clearBtn.classList.remove('show');
        applySearch('');
        searchInput.focus();
    });

    resetSearch.addEventListener('click', function (e) {
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

});

/* ── Delete Modal Functions ─────────────────────────────────── */

const deleteTypes = {
    date: {
        title: 'Hapus Berdasarkan Tanggal Spesifik',
        fields: '<div><label class="filter-label">Pilih Tanggal</label><input type="date" name="tanggal" class="form-control" required></div>',
        route: '{{ route("admin.activity-log.delete-by-date") }}',
        statsParam: 'date'
    },
    month: {
        title: 'Hapus Berdasarkan Bulan',
        fields: '<div><label class="filter-label">Pilih Bulan & Tahun</label><input type="month" name="bulan" class="form-control" required></div>',
        route: '{{ route("admin.activity-log.delete-by-month") }}',
        statsParam: 'month'
    },
    year: {
        title: 'Hapus Berdasarkan Tahun',
        fields: '<div><label class="filter-label">Pilih Tahun</label><input type="number" name="tahun" class="form-control" min="2000" max="2099" placeholder="2024" required></div>',
        route: '{{ route("admin.activity-log.delete-by-year") }}',
        statsParam: 'year'
    },
    range: {
        title: 'Hapus Berdasarkan Rentang Tanggal',
        fields: '<div class="delete-form-row"><div><label class="filter-label">Dari Tanggal</label><input type="date" name="dari_tanggal" class="form-control" required></div><div><label class="filter-label">Sampai Tanggal</label><input type="date" name="sampai_tanggal" class="form-control" required></div></div>',
        route: '{{ route("admin.activity-log.delete-by-range") }}',
        statsParam: 'range'
    },
    older: {
        title: 'Hapus Log yang Lebih Lama dari X Hari',
        fields: '<div><label class="filter-label">Jumlah Hari (misal: 90 = lebih dari 90 hari lalu)</label><input type="number" name="hari" class="form-control" min="1" max="3650" value="90" required></div>',
        route: '{{ route("admin.activity-log.delete-older-than") }}',
        statsParam: 'older'
    },
    module: {
        title: 'Hapus Berdasarkan Module',
        fields: '<div><label class="filter-label">Pilih Module</label><select name="module" class="form-control" required><option value="">-- Pilih Module --</option><option value="cuti">Cuti</option><option value="absensi">Absensi</option><option value="shift">Shift</option><option value="jadwal_shift">Jadwal Shift</option><option value="karyawan">Karyawan</option><option value="user">User</option><option value="wajah">Wajah</option></select></div>',
        route: '{{ route("admin.activity-log.delete-by-module") }}',
        statsParam: 'module'
    },
    action: {
        title: 'Hapus Berdasarkan Tipe Aksi',
        fields: '<div><label class="filter-label">Pilih Tipe Aksi</label><select name="action" class="form-control" required><option value="">-- Pilih Aksi --</option><option value="create">Create</option><option value="update">Update</option><option value="delete">Delete</option><option value="approve">Approve</option><option value="reject">Reject</option></select></div>',
        route: '{{ route("admin.activity-log.delete-by-action") }}',
        statsParam: 'action'
    },
    all: {
        title: 'Hapus Semua Log',
        fields: '<div class="alert alert-danger" role="alert"><strong><i class="fas fa-exclamation-circle me-2"></i>PERINGATAN!</strong><br>Anda akan menghapus SEMUA log dalam sistem. Tindakan ini tidak dapat dibatalkan.<br><br><label class="mt-2"><input type="checkbox" name="confirm" required> Saya memahami resiko dan ingin melanjutkan</label></div>',
        route: '{{ route("admin.activity-log.delete-all") }}',
        statsParam: 'all'
    }
};

function showDeleteModal(type) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const typeGrid = document.getElementById('deleteTypeGrid');
    const subtitle = document.getElementById('deleteModalSubtitle');
    const config = deleteTypes[type] || deleteTypes['date'];

    subtitle.textContent = config.title;
    typeGrid.innerHTML = config.fields;
    form.action = config.route;

    // Add event listeners for real-time count preview
    const inputs = typeGrid.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('change', updateCountPreview);
        input.addEventListener('input', updateCountPreview);
    });

    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(() => inputs[0]?.focus(), 200);

    updateCountPreview();
}

function updateCountPreview() {
    const form = document.getElementById('deleteForm');
    const formData = new FormData(form);
    const countPreview = document.getElementById('countPreview');
    const countValue = document.getElementById('countValue');
    const countLabel = document.getElementById('countLabel');
    const btnConfirm = document.getElementById('btnConfirmDelete');

    // Determine which type of deletion
    let type = null;
    let params = '?';

    if (formData.has('tanggal')) {
        type = 'date';
        params += 'type=date&date=' + formData.get('tanggal');
    } else if (formData.has('bulan')) {
        type = 'month';
        params += 'type=month&month=' + formData.get('bulan');
    } else if (formData.has('tahun')) {
        type = 'year';
        params += 'type=year&year=' + formData.get('tahun');
    } else if (formData.has('dari_tanggal')) {
        type = 'range';
        params += 'type=range&dari_tanggal=' + formData.get('dari_tanggal') + '&sampai_tanggal=' + formData.get('sampai_tanggal');
    } else if (formData.has('hari')) {
        type = 'older';
        params += 'type=older&hari=' + formData.get('hari');
    } else if (formData.has('module')) {
        type = 'module';
        params += 'type=module&module=' + formData.get('module');
    } else if (formData.has('action')) {
        type = 'action';
        params += 'type=action&action=' + formData.get('action');
    }

    if (type) {
        fetch('{{ route("admin.activity-log.api.delete-stats") }}' + params)
            .then(r => r.json())
            .then(data => {
                countValue.textContent = data.count;
                countLabel.textContent = `log akan dihapus ${data.oldest_date ? '(dari ' + data.oldest_date + ')' : ''}`;
                countPreview.classList.add('show');
                btnConfirm.disabled = data.count === 0;
            })
            .catch(e => console.error('Error:', e));
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.getElementById('deleteForm').reset();
}

document.getElementById('deleteForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('btnConfirmDelete');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Gagal menghapus log', 'error');
        }
        closeDeleteModal();
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Ya, Hapus';
    })
    .catch(e => {
        showToast('Error: ' + e.message, 'error');
        closeDeleteModal();
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Ya, Hapus';
    });
});

document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDeleteModal();
});

function showToast(msg, type = 'success') {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed; top:20px; right:20px;
        padding:14px 22px;
        background:${type === 'success' ? '#16a34a' : '#dc2626'};
        color:white; border-radius:8px; font-weight:600; font-size:13px;
        z-index:10000; box-shadow:0 4px 14px rgba(0,0,0,.15);
        animation:slideInRight .3s ease-out;
    `;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${msg}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideInRight .3s ease-out reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

@if(session('success'))
    setTimeout(() => showToast('{{ session('success') }}', 'success'), 500);
@endif
</script>

<style>
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
</style>
@endpush