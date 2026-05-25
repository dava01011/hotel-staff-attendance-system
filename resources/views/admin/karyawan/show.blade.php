@extends('admin.layouts.app')

@section('title', 'Info Karyawan')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #ea580c 0%, #1e40af 100%);
        border-radius: 12px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }

    .profile-header::after {
        content: '';
        position: absolute;
        right: 0;
        bottom: 0;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(30%, 30%);
    }

    .profile-photo-container {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        padding: 5px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        position: relative;
        z-index: 2;
    }

    .profile-photo {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .info-card-header {
        background: transparent;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-card-header i {
        color: #ea580c;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-item {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        width: 180px;
        flex-shrink: 0;
    }

    .info-value {
        color: #334155;
        font-weight: 600;
        flex: 1;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-badge.aktif { background: #dcfce7; color: #166534; }
    .status-badge.nonaktif { background: #fee2e2; color: #991b1b; }
    
    .face-preview {
        width: 100%;
        max-width: 250px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-top: 15px;
    }

    /* ══════ RESPONSIVE ══════ */
    @media (max-width: 767.98px) {
        .profile-header {
            padding: 20px;
            text-align: center;
            align-items: center;
        }

        .profile-photo-container {
            width: 90px;
            height: 90px;
        }

        .profile-header h2 {
            font-size: 1.25rem;
        }

        .profile-header .d-flex.flex-wrap {
            justify-content: center;
        }

        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            padding: 12px 16px;
        }

        .info-label {
            width: 100%;
            font-size: 12px;
        }

        .info-value {
            width: 100%;
            font-size: 14px;
        }

        .info-card-header {
            padding: 16px;
            font-size: 14px;
        }

        .page-header-row {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px;
        }
    }

    @media (max-width: 575.98px) {
        .profile-header {
            padding: 16px;
        }

        .profile-photo-container {
            width: 72px;
            height: 72px;
        }

        .profile-header h2 {
            font-size: 1.1rem;
        }

        .profile-header .badge {
            font-size: 11px;
            padding: 5px 10px !important;
        }

        .info-item {
            padding: 10px 14px;
        }

        .info-card-header {
            padding: 14px;
            font-size: 13px;
            flex-wrap: wrap;
            gap: 8px;
        }
    }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 page-header-row">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Info Karyawan</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 14px;">
                <li class="breadcrumb-item"><a href="{{ route('admin.karyawan.index') }}" class="text-decoration-none">Data Karyawan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Info Detail</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-light border shadow-sm">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="profile-header d-flex flex-column flex-md-row align-items-md-center gap-4">
            <div class="profile-photo-container">
                <img src="{{ $karyawan->foto_profil ? asset('storage/'.$karyawan->foto_profil) : asset('images/default-avatar.png') }}" alt="{{ $karyawan->user->nama }}" class="profile-photo">
            </div>
            <div>
                <p class="mb-1" style="opacity: 0.8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Full Name</p>
                <h2 class="fw-bold mb-1" style="color: white;">{{ $karyawan->user->nama }}</h2>
                <p class="mb-2" style="opacity: 0.9; font-size: 16px;">
                    <i class="fas fa-id-badge me-2"></i>{{ $karyawan->nip }}
                </p>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill">
                        <i class="fas fa-building me-1"></i> {{ $karyawan->departemen->nama ?? '-' }}
                    </span>
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill">
                        <i class="fas fa-briefcase me-1"></i> {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                    </span>
                    <span class="badge {{ $karyawan->status == 'aktif' ? 'bg-success' : 'bg-danger' }} text-white px-3 py-2 rounded-pill">
                        <i class="fas {{ $karyawan->status == 'aktif' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i> {{ ucfirst($karyawan->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- PERSONAL DATA --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div class="col-lg-8">
        <div class="info-card">
            <div class="info-card-header d-flex justify-content-between align-items-center">
                <div><i class="fas fa-user"></i> Personal Data</div>
                @if($canCRUD)
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPersonalModal">
                        <i class="fas fa-edit me-1"></i> Edit
                    </button>
                @endif
            </div>
            <ul class="info-list">
                <li class="info-item">
                    <div class="info-label">NIP</div>
                    <div class="info-value">{{ $karyawan->nip }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $karyawan->user->nama }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Departemen</div>
                    <div class="info-value">{{ $karyawan->departemen->nama ?? '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Jabatan</div>
                    <div class="info-value">{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge {{ $karyawan->status }}">
                            <i class="fas {{ $karyawan->status == 'aktif' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ ucfirst($karyawan->status) }}
                        </span>
                    </div>
                </li>
                <li class="info-item">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $karyawan->no_telepon ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $karyawan->user->email }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Additional Phone</div>
                    <div class="info-value">{{ $karyawan->no_telepon_tambahan ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Place of Birth</div>
                    <div class="info-value">{{ $karyawan->tempat_lahir ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Birthdate</div>
                    <div class="info-value">{{ $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('d M Y') : '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="info-value">
                        @if($karyawan->jenis_kelamin === 'laki-laki') Laki-laki
                        @elseif($karyawan->jenis_kelamin === 'perempuan') Perempuan
                        @else - @endif
                    </div>
                </li>
                <li class="info-item">
                    <div class="info-label">Marital Status</div>
                    <div class="info-value">
                        @if($karyawan->status_pernikahan === 'belum_menikah') Belum Menikah
                        @elseif($karyawan->status_pernikahan === 'menikah') Menikah
                        @elseif($karyawan->status_pernikahan === 'cerai') Cerai
                        @else - @endif
                    </div>
                </li>
                <li class="info-item">
                    <div class="info-label">Blood Type</div>
                    <div class="info-value">{{ $karyawan->golongan_darah ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Religion</div>
                    <div class="info-value">{{ $karyawan->agama ? ucfirst($karyawan->agama) : '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Bergabung Sejak</div>
                    <div class="info-value">{{ $karyawan->created_at->format('d M Y') }}</div>
                </li>
            </ul>
        </div>

        {{-- ════ IDENTITY & ADDRESS ════ --}}
        <div class="info-card">
            <div class="info-card-header d-flex justify-content-between align-items-center">
                <div><i class="fas fa-address-card"></i> Identity & Address</div>
                @if($canCRUD)
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editIdentityModal">
                        <i class="fas fa-edit me-1"></i> Edit
                    </button>
                @endif
            </div>
            <ul class="info-list">
                <li class="info-item">
                    <div class="info-label">NIK (16 digit)</div>
                    <div class="info-value">{{ $karyawan->nik ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Citizen ID Address</div>
                    <div class="info-value">{{ $karyawan->alamat_ktp ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Postal Code</div>
                    <div class="info-value">{{ $karyawan->kode_pos ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Residential Address</div>
                    <div class="info-value">{{ $karyawan->alamat_tinggal ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Passport Number</div>
                    <div class="info-value">{{ $karyawan->no_paspor ?: '-' }}</div>
                </li>
                <li class="info-item">
                    <div class="info-label">Passport Expiry Date</div>
                    <div class="info-value">{{ $karyawan->masa_berlaku_paspor ? $karyawan->masa_berlaku_paspor->format('d M Y') : '-' }}</div>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="info-card">
            <div class="info-card-header">
                <i class="fas fa-fingerprint"></i> Autentikasi Wajah
            </div>
            <div class="card-body p-4 text-center">
                @if($karyawan->wajah_terdaftar && $wajahKaryawan)
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 40px;"></i>
                    </div>
                    <h6 class="fw-bold text-success mb-2">Terdaftar</h6>
                    <p class="text-muted small mb-0">Wajah telah diverifikasi untuk absensi.</p>
                    @if($wajahKaryawan->foto_wajah)
                        <img src="{{ asset('storage/'.$wajahKaryawan->foto_wajah) }}" class="face-preview" alt="Foto Wajah Terdaftar">
                    @endif
                @else
                    <div class="mb-3">
                        <i class="fas fa-exclamation-circle text-warning" style="font-size: 40px;"></i>
                    </div>
                    <h6 class="fw-bold text-warning mb-2">Belum Terdaftar</h6>
                    <p class="text-muted small mb-0">Karyawan ini belum mendaftarkan wajah untuk absensi.</p>
                @endif
            </div>
        </div>
        
        <div class="info-card">
            <div class="info-card-header">
                <i class="fas fa-calendar-check"></i> Jatah Cuti Tahunan ({{ date('Y') }})
            </div>
            <div class="card-body p-4">
                @php
                    $jatah = $karyawan->jatahCuti->first();
                @endphp
                @if($jatah)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Jatah Cuti</span>
                        <span class="fw-bold fs-5">{{ $jatah->jatah }} <small class="text-muted fs-6">Hari</small></span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        @php
                            $used = $jatah->jatah_awal > 0 ? $jatah->jatah_awal - $jatah->jatah : 0;
                            $pct = $jatah->jatah_awal > 0 ? ($used / $jatah->jatah_awal) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%"></div>
                    </div>
                @else
                    <div class="text-center text-muted">Belum ada data jatah cuti</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@if($canCRUD)
<!-- Modal Edit Personal Data -->
<div class="modal fade" id="editPersonalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.karyawan.update-section', $karyawan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="personal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2 text-primary"></i>Edit Personal Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="no_telepon" class="form-control" value="{{ $karyawan->no_telepon }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Additional Phone</label>
                            <input type="text" name="no_telepon_tambahan" class="form-control" value="{{ $karyawan->no_telepon_tambahan }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Place of Birth</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ $karyawan->tempat_lahir }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Birthdate</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gender</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="laki-laki" {{ $karyawan->jenis_kelamin == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ $karyawan->jenis_kelamin == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Marital Status</label>
                            <select name="status_pernikahan" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="belum_menikah" {{ $karyawan->status_pernikahan == 'belum_menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                <option value="menikah" {{ $karyawan->status_pernikahan == 'menikah' ? 'selected' : '' }}>Menikah</option>
                                <option value="cerai" {{ $karyawan->status_pernikahan == 'cerai' ? 'selected' : '' }}>Cerai</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Blood Type</label>
                            <select name="golongan_darah" class="form-control">
                                <option value="">-- Pilih --</option>
                                @foreach(['A', 'B', 'AB', 'O'] as $gd)
                                    <option value="{{ $gd }}" {{ $karyawan->golongan_darah == $gd ? 'selected' : '' }}>{{ $gd }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Religion</label>
                            <select name="agama" class="form-control">
                                <option value="">-- Pilih --</option>
                                @foreach(['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu'] as $ag)
                                    <option value="{{ $ag }}" {{ $karyawan->agama == $ag ? 'selected' : '' }}>{{ ucfirst($ag) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Identity & Address -->
<div class="modal fade" id="editIdentityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.karyawan.update-section', $karyawan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="identity">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fas fa-address-card me-2 text-primary"></i>Edit Identity & Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">NIK (16 digit)</label>
                            <input type="text" name="nik" class="form-control" value="{{ $karyawan->nik }}" maxlength="16">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Postal Code</label>
                            <input type="text" name="kode_pos" class="form-control" value="{{ $karyawan->kode_pos }}" maxlength="10">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Citizen ID Address (Alamat KTP)</label>
                            <textarea name="alamat_ktp" class="form-control" rows="3">{{ $karyawan->alamat_ktp }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Residential Address (Alamat Tinggal)</label>
                            <textarea name="alamat_tinggal" class="form-control" rows="3">{{ $karyawan->alamat_tinggal }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Passport Number</label>
                            <input type="text" name="no_paspor" class="form-control" value="{{ $karyawan->no_paspor }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Passport Expiry Date</label>
                            <input type="date" name="masa_berlaku_paspor" class="form-control" value="{{ $karyawan->masa_berlaku_paspor ? $karyawan->masa_berlaku_paspor->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

