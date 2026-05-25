@extends('admin.layouts.app')

@section('title', 'Data Absensi')

@push('styles')
<style>
    /* ── Search Box ─────────────────────────────────────────── */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 400px;
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

    .clear-search:hover {
        color: #dc3545;
    }

    .clear-search.show {
        display: block;
    }

    /* ── Date Input ─────────────────────────────────────────── */
    .date-input {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 7px 12px;
        font-size: 13px;
        color: #334155;
        transition: border-color 0.2s;
    }

    .date-input:focus {
        border-color: #0d6efd;
        outline: none;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, .1);
    }

    /* ── Quick Date Buttons ─────────────────────────────────── */
    .quick-date-btns {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .quick-date-btn {
        padding: 5px 13px;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 5px;
        color: #495057;
    }

    .quick-date-btn:hover {
        border-color: #0d6efd;
        background: #f8f9fa;
    }

    .quick-date-btn.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    /* ── Status Filter Pills ────────────────────────────────── */
    .filter-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

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
        color: #495057;
    }

    .filter-btn:hover {
        border-color: #0d6efd;
        background: #f8f9fa;
    }

    .filter-btn.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    .filter-btn .count {
        background: rgba(0, 0, 0, .1);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
    }

    .filter-btn.active .count {
        background: rgba(255, 255, 255, .2);
    }

    /* ── Table ──────────────────────────────────────────────── */
    .table-compact th {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 11px 10px;
        color: #495057;
    }

    .table-compact td {
        padding: 9px 10px;
        vertical-align: middle;
        font-size: 13px;
    }

    .table tbody tr {
        transition: background-color 0.15s;
    }

    .table tbody tr.highlight {
        background-color: #fff3cd !important;
        animation: highlight-fade 1.5s ease-out;
    }

    @keyframes highlight-fade {
        from {
            background-color: #fff3cd;
        }

        to {
            background-color: transparent;
        }
    }

    /* ── Employee Cell ──────────────────────────────────────── */
    .employee-compact {
        display: flex;
        align-items: center;
        gap: 10px;
    }

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

    .employee-name {
        font-weight: 600;
        color: #2d3748;
        line-height: 1.2;
    }

    .employee-dept {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 1px;
    }

    /* ── Time Val ───────────────────────────────────────────── */
    .time-val {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    /* ── Location Badge ─────────────────────────────────────── */
    .location-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        background: #e0f2fe;
        color: #0369a1;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .location-badge:hover {
        background: #bae6fd;
        transform: scale(1.04);
    }

    /* ── Photo ──────────────────────────────────────────────── */
    .photo-preview {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid #e5e7eb;
        transition: all 0.2s;
        display: block;
    }

    .photo-preview:hover {
        border-color: #3b82f6;
        transform: scale(1.1);
    }

    .photo-placeholder {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d1d5db;
        font-size: 15px;
    }

    /* ── Face Badge ─────────────────────────────────────────── */
    .face-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }

    .face-badge.valid {
        background: #d1fae5;
        color: #065f46;
    }

    .face-badge.invalid {
        background: #fee2e2;
        color: #991b1b;
    }

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

    .status-badge i {
        font-size: 9px;
    }

    .status-badge.hadir {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.izin {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.sakit {
        background: #fed7aa;
        color: #9a3412;
    }

    .status-badge.cuti {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-badge.alpa {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ── No Results / Search Stats ──────────────────────────── */
    .no-results {
        display: none;
        text-align: center;
        padding: 60px 20px;
    }

    .no-results.show {
        display: block;
    }

    .no-results-icon {
        font-size: 56px;
        color: #dee2e6;
        margin-bottom: 16px;
    }

    .no-results-text {
        color: #6c757d;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .no-results-subtext {
        color: #adb5bd;
        font-size: 13px;
    }

    .search-stats {
        font-size: 14px;
        color: #6c757d;
        display: none;
        padding: 12px 0;
        border-top: 1px solid #e9ecef;
        margin-top: 14px;
    }

    .search-stats.show {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-stats strong {
        color: #0d6efd;
    }

    .reset-filter {
        font-size: 13px;
        color: #dc3545;
        text-decoration: none;
        font-weight: 600;
    }

    .reset-filter:hover {
        text-decoration: underline;
    }

    /* ── Export Buttons ─────────────────────────────────────── */
    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-export:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
    }

    .btn-export.excel {
        background: #16a34a;
        color: white;
    }

    .btn-export.excel:hover {
        background: #15803d;
        color: white;
    }

    .btn-export.pdf {
        background: #dc2626;
        color: white;
    }

    .btn-export.pdf:hover {
        background: #b91c1c;
        color: white;
    }

    /* ── Image Lightbox ─────────────────────────────────────── */
    .img-lightbox {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0, 0, 0, .88);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .img-lightbox.show {
        display: flex;
    }

    .img-lightbox img {
        max-width: 90%;
        max-height: 90vh;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .5);
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: white;
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        color: #374151;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .lightbox-close:hover {
        background: #f3f4f6;
        transform: scale(1.1);
    }

    /* ── Modal PDF Preview ──────────────────────────────────── */
    .modal-pdf .modal-dialog {
        max-width: 95vw;
        height: 95vh;
        margin: 1.5rem auto;
    }

    .modal-pdf .modal-content {
        height: 100%;
        border-radius: 16px;
        overflow: hidden;
    }

    .modal-pdf .modal-body {
        padding: 0;
        height: calc(100% - 120px);
    }

    .modal-pdf .pdf-frame {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .search-container {
            max-width: 100%;
            margin-bottom: 12px;
        }
    }

    @media (max-width: 768px) {
        .header-row {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }

        .avatar-small {
            width: 30px;
            height: 30px;
            font-size: 11px;
        }

        .photo-preview,
        .photo-placeholder {
            width: 34px;
            height: 34px;
        }
    }
</style>
@endpush

@section('content')

{{-- ── Page Header ──────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 header-row">
    <div>
        <h4 class="fw-bold mb-1">Data Absensi</h4>
        <small class="text-muted">
            @if($canCRUD)
            Laporan kehadiran karyawan
            @elseif($userRole === 'admin')
            Laporan kehadiran departemen Anda
            @elseif($userRole === 'manager' || $userRole === 'gm')
            Laporan kehadiran tim Anda
            @else
            Laporan kehadiran
            @endif
        </small>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex gap-2">
        @if($canCRUD)
        <button type="button" class="btn btn-primary btn-export" data-bs-toggle="modal" data-bs-target="#addAbsensiModal">
            <i class="fas fa-plus-circle"></i> Tambah Absen Manual
        </button>
        @endif
        <a id="btnExportExcel"
            href="#"
            class="btn-export excel">
            <i class="fas fa-file-excel"></i> Excel
        </a>
        <a id="btnExportPdf"
            href="#"
            class="btn-export pdf">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
    </div>
</div>

{{-- ── Search & Filter Card ─────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">

        {{-- Search + Date Range --}}
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text"
                    class="form-control search-input"
                    id="searchInput"
                    placeholder="Cari nama karyawan..."
                    autocomplete="off">
                <button class="clear-search" id="clearSearch" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <input type="date" class="form-control date-input" id="dateFrom" style="width:148px;">
                <span class="text-muted" style="font-size:13px;">s/d</span>
                <input type="date" class="form-control date-input" id="dateTo" style="width:148px;">
            </div>

            <div class="ms-auto text-muted">
                <small>Total: <strong>{{ $absensi->count() }}</strong> data</small>
            </div>
        </div>

        {{-- Quick Date Shortcuts --}}
        <div class="quick-date-btns mt-3">
            <button class="quick-date-btn active" data-range="all">
                <i class="fas fa-calendar"></i> Semua
            </button>
            <button class="quick-date-btn" data-range="week">
                <i class="fas fa-calendar-week"></i> Minggu Ini
            </button>
            <button class="quick-date-btn" data-range="month">
                <i class="fas fa-calendar-alt"></i> Bulan Ini
            </button>
        </div>

        {{-- Status Filter --}}
        <div class="filter-group mt-3">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-list"></i> Semua
                <span class="count">{{ $absensi->count() }}</span>
            </button>
            <button class="filter-btn" data-filter="hadir">
                <i class="fas fa-check-circle"></i> Hadir
                <span class="count">{{ $absensi->where('status','hadir')->count() }}</span>
            </button>
            <button class="filter-btn" data-filter="izin">
                <i class="fas fa-file-alt"></i> Izin
                <span class="count">{{ $absensi->where('status','izin')->count() }}</span>
            </button>
            <button class="filter-btn" data-filter="sakit">
                <i class="fas fa-notes-medical"></i> Sakit
                <span class="count">{{ $absensi->where('status','sakit')->count() }}</span>
            </button>
            <button class="filter-btn" data-filter="cuti">
                <i class="fas fa-plane"></i> Cuti
                <span class="count">{{ $absensi->where('status','cuti')->count() }}</span>
            </button>
            <button class="filter-btn" data-filter="alpa">
                <i class="fas fa-times-circle"></i> Tidak Hadir
                <span class="count">{{ $absensi->where('status','alpa')->count() }}</span>
            </button>
        </div>

        {{-- Search Stats --}}
        <div class="search-stats" id="searchStats">
            <div>
                Menampilkan <strong id="resultCount">0</strong>
                dari <strong>{{ $absensi->count() }}</strong> data
            </div>
            <a href="#" class="reset-filter" id="resetFilter">
                <i class="fas fa-redo"></i> Reset Filter
            </a>
        </div>

    </div>
</div>

{{-- ── INFO BADGE untuk Admin/Manager/GM ───────────────── --}}
@if(!$canCRUD)
<div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-info-circle"></i>
    <strong>Informasi:</strong>
    @if($userRole === 'admin')
    Anda hanya dapat melihat data absensi karyawan di departemen Anda.
    @elseif($userRole === 'manager' || $userRole === 'gm')
    Anda hanya dapat melihat data absensi tim/departemen Anda.
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- ── Table Card ───────────────────────────────────────── --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-compact align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:44px; padding-left:16px;">#</th>
                        <th style="min-width:180px;">Karyawan</th>
                        <th style="width:105px;">Tanggal</th>
                        <th style="width:88px;">Jam Masuk</th>
                        <th style="width:88px;">Jam Pulang</th>
                        <th style="width:72px; text-align:center;">Lokasi</th>
                        <th style="width:78px; text-align:center;">Foto In</th>
                        <th style="width:78px; text-align:center;">Foto Out</th>
                        <th style="width:88px; text-align:center;">Wajah</th>
                        <th style="width:108px;">Status</th>
                        @if($canCRUD)
                        <th style="width:100px; text-align:center;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="absensiTableBody">
                    @forelse ($absensi as $item)
                    @php
                    $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
                    $ci = ord(strtolower(substr($item->karyawan->user->nama ?? 'k', 0, 1))) % count($colors);
                    $st = strtolower($item->status);
                    $stIcon = match($st) {
                    'hadir' => 'check-circle',
                    'izin' => 'file-alt',
                    'sakit' => 'notes-medical',
                    'cuti' => 'plane',
                    default => 'times-circle',
                    };
                    @endphp
                    <tr data-nama="{{ strtolower($item->karyawan->user->nama ?? '') }}"
                        data-tanggal="{{ $item->tanggal }}"
                        data-status="{{ $st }}">

                        <td style="padding-left:16px; color:#94a3b8; font-size:12px;">
                            {{ $loop->iteration }}
                        </td>

                        {{-- Karyawan --}}
                        <td>
                            <div class="employee-compact">
                                <div class="avatar-small" style="background:{{ $colors[$ci] }};">
                                    {{ strtoupper(substr($item->karyawan->user->nama ?? 'K', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="employee-name">{{ $item->karyawan->user->nama ?? '-' }}</div>
                                    <div class="employee-dept">{{ $item->karyawan->departemen->nama ?? '-' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td>
                            <div style="font-weight:600; font-size:13px; color:#334155;">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </div>
                            <div style="font-size:11px; color:#94a3b8;">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('D') }}
                            </div>
                        </td>

                        {{-- Jam Masuk --}}
                        <td>
                            @if($item->jam_masuk)
                            <span class="time-val">{{ substr($item->jam_masuk, 0, 5) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Jam Pulang --}}
                        <td>
                            @if($item->jam_pulang)
                            <span class="time-val">{{ substr($item->jam_pulang, 0, 5) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Lokasi --}}
                        <td style="text-align:center;">
                            @if($item->latitude && $item->longitude)
                            <a class="location-badge"
                                href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                target="_blank">
                                <i class="fas fa-map-marker-alt"></i> Map
                            </a>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Foto Masuk --}}
                        <td style="text-align:center;">
                            @if($item->foto_masuk)
                            <img src="{{ asset('storage/' . $item->foto_masuk) }}"
                                class="photo-preview mx-auto"
                                onclick="showLightbox(this.src)"
                                alt="Foto Masuk">
                            @else
                            <div class="photo-placeholder mx-auto">
                                <i class="fas fa-camera-slash"></i>
                            </div>
                            @endif
                        </td>

                        {{-- Foto Pulang --}}
                        <td style="text-align:center;">
                            @if($item->foto_pulang)
                            <img src="{{ asset('storage/' . $item->foto_pulang) }}"
                                class="photo-preview mx-auto"
                                onclick="showLightbox(this.src)"
                                alt="Foto Pulang">
                            @else
                            <div class="photo-placeholder mx-auto">
                                <i class="fas fa-camera-slash"></i>
                            </div>
                            @endif
                        </td>

                        {{-- Wajah --}}
                        <td style="text-align:center;">
                            @if($item->face_valid == 1)
                            <span class="face-badge valid">
                                <i class="fas fa-check-circle"></i> Valid
                            </span>
                            @elseif($item->face_valid == 0)
                            <span class="face-badge invalid">
                                <i class="fas fa-times-circle"></i> Invalid
                            </span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="status-badge {{ $st }}">
                                <i class="fas fa-{{ $stIcon }}"></i>
                                {{ $item->status === 'alpa' ? 'Tidak Hadir' : ucfirst($item->status) }}
                            </span>
                        </td>

                        @if($canCRUD)
                        <td style="text-align:center;">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="editAbsensi({{ json_encode([
                                        'id' => $item->id,
                                        'nama' => $item->karyawan->user->nama,
                                        'tanggal' => $item->tanggal->format('Y-m-d'),
                                        'jam_masuk' => $item->jam_masuk ? substr($item->jam_masuk, 0, 5) : '',
                                        'jam_pulang' => $item->jam_pulang ? substr($item->jam_pulang, 0, 5) : '',
                                        'status' => $item->status
                                    ]) }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.absensi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data absensi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif

                    </tr>
                    @empty
                    <tr id="emptyState">
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-clipboard-list fa-3x mb-3" style="opacity:.25;"></i>
                                <p class="mb-0 fw-medium">Belum ada data absensi</p>
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
            <div class="no-results-text">Tidak ada data ditemukan</div>
            <div class="no-results-subtext">Coba ubah kata kunci atau filter pencarian</div>
        </div>

    </div>
</div>

{{-- ── Image Lightbox ────────────────────────────────────── --}}
<div class="img-lightbox" id="imgLightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">
        <i class="fas fa-times"></i>
    </button>
    <img src="" id="lightboxImg" onclick="event.stopPropagation()" alt="Foto Absensi">
</div>

{{-- ── Modal Preview PDF ─────────────────────────────────── --}}
<div class="modal fade modal-pdf" id="previewPdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-pdf text-danger me-2"></i>Preview Laporan Absensi (PDF)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfPreviewFrame" class="pdf-frame" src=""></iframe>
            </div>
            <div class="modal-footer bg-light">
                <div class="d-flex gap-2 w-100 justify-content-between align-items-center">
                    <small class="text-muted" id="pdfFilterInfo"></small>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-success" id="btnDownloadPdf" target="_blank">
                            <i class="fas fa-download me-1"></i>Download PDF
                        </a>
                        <button type="button" class="btn btn-primary" id="btnPrintPdf">
                            <i class="fas fa-print me-1"></i>Cetak
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Preview Excel ─────────────────────────────────── --}}
<div class="modal fade" id="previewExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-excel text-success me-2"></i>Preview Laporan Absensi (Excel)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" style="overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted" id="excelFilterInfo"></small>
                    <small class="text-muted">Menampilkan maksimal 100 data pertama</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Departemen</th>
                                <th>Jabatan</th>
                                <th>Terlambat</th>
                                <th>Wajah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="excelPreviewBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <div class="d-flex gap-2 w-100 justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" class="btn btn-success" id="btnDownloadExcel">Download Excel</a>
                </div>
            </div>
        </div>
    </div>
</div>

@if($canCRUD)
{{-- ── Modal Tambah Absensi Manual ────────────────────────── --}}
<div class="modal fade" id="addAbsensiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.absensi.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Absensi Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Karyawan</label>
                    <select name="karyawan_id" class="form-select" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($karyawanList as $k)
                        <option value="{{ $k->id }}">{{ $k->user->nama }} ({{ $k->nip }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="form-control">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="hadir">Hadir</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                        <option value="alpa">Tidak Hadir (Alpa)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Absensi</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal Edit Absensi ────────────────────────────────── --}}
<div class="modal fade" id="editAbsensiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editAbsensiForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Karyawan</label>
                    <input type="text" id="edit_nama" class="form-control" readonly disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" id="edit_tanggal" class="form-control" readonly disabled>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="edit_jam_masuk" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jam Pulang</label>
                        <input type="time" name="jam_pulang" id="edit_jam_pulang" class="form-control">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" id="edit_status" class="form-select" required>
                        <option value="hadir">Hadir</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                        <option value="alpa">Tidak Hadir (Alpa)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Elements ─────────────────────────────────────────────
        const searchInput = document.getElementById('searchInput');
        const clearBtn = document.getElementById('clearSearch');
        const dateFrom = document.getElementById('dateFrom');
        const dateTo = document.getElementById('dateTo');
        const quickBtns = document.querySelectorAll('.quick-date-btn');
        const filterBtns = document.querySelectorAll('.filter-btn');
        const tableBody = document.getElementById('absensiTableBody');
        const noResults = document.getElementById('noResults');
        const searchStats = document.getElementById('searchStats');
        const resultCount = document.getElementById('resultCount');
        const resetFilter = document.getElementById('resetFilter');
        const allRows = tableBody.querySelectorAll('tr:not(#emptyState)');
        const btnExcelLink = document.getElementById('btnExportExcel');
        const btnPdfLink = document.getElementById('btnExportPdf');

        // Modal PDF
        const previewPdfModal = new bootstrap.Modal(document.getElementById('previewPdfModal'));
        const pdfFrame = document.getElementById('pdfPreviewFrame');
        const btnDownloadPdf = document.getElementById('btnDownloadPdf');
        const btnPrintPdf = document.getElementById('btnPrintPdf');
        const pdfFilterInfo = document.getElementById('pdfFilterInfo');

        // Modal Excel
        const previewExcelModal = new bootstrap.Modal(document.getElementById('previewExcelModal'));
        const excelPreviewBody = document.getElementById('excelPreviewBody');
        const btnDownloadExcel = document.getElementById('btnDownloadExcel');
        const excelFilterInfo = document.getElementById('excelFilterInfo');

        const baseExcelDownload = "{{ route('admin.absensi.export-excel') }}";
        const basePreviewPdf = "{{ route('admin.absensi.preview-pdf') }}";
        const baseDownloadPdf = "{{ route('admin.absensi.export-pdf') }}";

        let currentSearch = '';
        let currentFilter = 'all';

        function fmt(d) {
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function getFirstDayOfWeek(date) {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day;
            return new Date(d.setDate(diff));
        }

        quickBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                quickBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const range = this.getAttribute('data-range');
                const now = new Date();
                let from = null,
                    to = null;

                if (range === 'week') {
                    const weekStart = getFirstDayOfWeek(now);
                    from = fmt(weekStart);
                    to = fmt(now);
                } else if (range === 'month') {
                    const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
                    from = fmt(monthStart);
                    to = fmt(now);
                }

                dateFrom.value = from || '';
                dateTo.value = to || '';
                applyFilters();
            });
        });

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                currentFilter = this.getAttribute('data-filter');
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                applyFilters();
            });
        });

        searchInput.addEventListener('input', function() {
            currentSearch = this.value.toLowerCase().trim();
            clearBtn.classList.toggle('show', currentSearch.length > 0);
            applyFilters();
        });

        dateFrom.addEventListener('change', applyFilters);
        dateTo.addEventListener('change', applyFilters);

        function applyFilters() {
            let visible = 0;

            allRows.forEach(row => {
                const nama = row.getAttribute('data-nama');
                const tanggal = row.getAttribute('data-tanggal');
                const status = row.getAttribute('data-status');

                const searchMatch = !currentSearch || nama.includes(currentSearch);
                const filterMatch = currentFilter === 'all' || status === currentFilter;

                let dateMatch = true;
                if (dateFrom.value || dateTo.value) {
                    if (dateFrom.value && tanggal < dateFrom.value) dateMatch = false;
                    if (dateTo.value && tanggal > dateTo.value) dateMatch = false;
                }

                if (searchMatch && filterMatch && dateMatch) {
                    row.style.display = '';
                    visible++;
                    if (currentSearch) {
                        row.classList.add('highlight');
                        setTimeout(() => row.classList.remove('highlight'), 1500);
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            updateUI(visible);
        }

        function updateUI(visible) {
            resultCount.textContent = visible;
            const active = currentSearch || currentFilter !== 'all' || dateFrom.value || dateTo.value;
            searchStats.classList.toggle('show', active);
            noResults.classList.toggle('show', visible === 0 && active);
        }

        function getQueryString() {
            const params = new URLSearchParams();
            if (currentSearch) params.set('search', currentSearch);
            if (currentFilter !== 'all') params.set('status', currentFilter);
            if (dateFrom.value) params.set('date_from', dateFrom.value);
            if (dateTo.value) params.set('date_to', dateTo.value);
            return params.toString();
        }

        function updateFilterInfo() {
            const parts = [];
            if (currentSearch) parts.push(`Pencarian: ${currentSearch}`);
            if (currentFilter !== 'all') parts.push(`Status: ${currentFilter}`);
            if (dateFrom.value) parts.push(`Dari: ${dateFrom.value}`);
            if (dateTo.value) parts.push(`Sampai: ${dateTo.value}`);
            const text = parts.length ? parts.join(' · ') : 'Semua data';
            if (pdfFilterInfo) pdfFilterInfo.textContent = text;
            if (excelFilterInfo) excelFilterInfo.textContent = text;
        }

        // ── PDF Preview ─────────────────────────────────────────
        btnPdfLink.addEventListener('click', function(e) {
            e.preventDefault();
            const qs = getQueryString();
            const previewUrl = basePreviewPdf + (qs ? '?' + qs : '');
            const downloadUrl = baseDownloadPdf + (qs ? '?' + qs : '');

            pdfFrame.src = previewUrl;
            btnDownloadPdf.href = downloadUrl;
            updateFilterInfo();
            previewPdfModal.show();
        });

        btnPrintPdf.addEventListener('click', function() {
            if (pdfFrame.contentWindow) {
                pdfFrame.contentWindow.focus();
                pdfFrame.contentWindow.print();
            } else {
                window.open(pdfFrame.src, '_blank')?.focus();
            }
        });

        // ── Excel Preview ────────────────────────────────────────
        async function loadExcelPreview() {
            const qs = getQueryString();
            const url = "{{ route('admin.absensi.preview-data') }}" + (qs ? '?' + qs : '');

            try {
                const response = await fetch(url);
                const data = await response.json();

                let html = '';
                data.data.forEach((item, index) => {
                    html += `<tr>
                    <td class="text-center">${index + 1}</td>
                    <td>${item.karyawan?.user?.nama ?? '-'}</td>
                    <td class="text-center">${item.tanggal_formatted}</td>
                    <td class="text-center">${item.jam_masuk?.substring(0,5) ?? '-'}</td>
                    <td class="text-center">${item.jam_pulang?.substring(0,5) ?? '-'}</td>
                    <td>${item.karyawan?.departemen?.nama ?? '-'}</td>
                    <td>${item.karyawan?.jabatan?.nama_jabatan ?? '-'}</td>
                    <td class="text-center">${item.terlambat ?? '-'}</td>
                    <td class="text-center">${item.face_valid == 1 ? 'Valid' : (item.face_valid == 0 ? 'Invalid' : '-')}</td>
                    <td class="text-center"><span class="badge bg-${getStatusColor(item.status)}">${item.status}</span></td>
                </tr>`;
                });

                if (data.data.length === 0) {
                    html = `<tr><td colspan="10" class="text-center py-4">Tidak ada data</td></tr>`;
                }

                excelPreviewBody.innerHTML = html;
                btnDownloadExcel.href = baseExcelDownload + (qs ? '?' + qs : '');
                updateFilterInfo();
            } catch (error) {
                excelPreviewBody.innerHTML = `<tr><td colspan="10" class="text-center py-4 text-danger">Gagal memuat data</td></tr>`;
            }
        }

        function getStatusColor(status) {
            return {
                'hadir': 'success',
                'izin': 'warning',
                'sakit': 'warning',
                'cuti': 'info',
                'alpha': 'danger'
            } [status] || 'secondary';
        }

        btnExcelLink.addEventListener('click', function(e) {
            e.preventDefault();
            loadExcelPreview();
            previewExcelModal.show();
        });

        // ── Clear & Reset ────────────────────────────────────────
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            currentSearch = '';
            clearBtn.classList.remove('show');
            applyFilters();
            searchInput.focus();
        });

        resetFilter.addEventListener('click', function(e) {
            e.preventDefault();
            searchInput.value = '';
            currentSearch = '';
            clearBtn.classList.remove('show');
            dateFrom.value = '';
            dateTo.value = '';
            currentFilter = 'all';

            filterBtns.forEach(b => b.classList.toggle('active', b.getAttribute('data-filter') === 'all'));
            quickBtns.forEach(b => b.classList.toggle('active', b.getAttribute('data-range') === 'all'));

            applyFilters();
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

        // Toast
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

        @if(session('success')) showToast('{{ session('
            success ') }}', 'success');
        @endif
        @if(session('error')) showToast('{{ session('
            error ') }}', 'error');
        @endif
    });

    // Lightbox
    function showLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('imgLightbox').classList.add('show');
    }

    function closeLightbox() {
        document.getElementById('imgLightbox').classList.remove('show');
    }

    @if($canCRUD)
    const editAbsensiModal = new bootstrap.Modal(document.getElementById('editAbsensiModal'));
    window.editAbsensi = function(data) {
        document.getElementById('editAbsensiForm').action = `/admin/absensi/${data.id}`;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_tanggal').value = data.tanggal;
        document.getElementById('edit_jam_masuk').value = data.jam_masuk;
        document.getElementById('edit_jam_pulang').value = data.jam_pulang;
        document.getElementById('edit_status').value = data.status;
        editAbsensiModal.show();
    }
    @endif

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeLightbox();
    });
</script>

<style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@endpush