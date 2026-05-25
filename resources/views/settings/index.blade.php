@extends(is_admin_mode() ? 'admin.layouts.app' : 'karyawan.layout.master')

@section('title', 'Pengaturan Akun')

@push('styles')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif; }

    body { margin: 0; padding: 0; background: #ffffff; overflow: hidden; }

    .fullscreen-wrapper {
        margin-bottom: 70px;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        display: flex; flex-direction: column;
        background: #ffffff;
    }

    @if(is_admin_mode())
    .fullscreen-wrapper { position: relative; height: auto; overflow: visible; margin-bottom: 0; }
    .settings-content   { overflow: visible; margin-top: 0 !important; height: auto; }
    body { overflow: auto !important; }
    @endif

    .settings-content { flex: 1; margin-top: 70px; overflow-y: auto; -webkit-overflow-scrolling: touch; background: #f8f9fa; }

    .profile-section { background: white; padding: 25px 20px; margin: 0; border-bottom: 1px solid #f0f0f0; }
    .profile-section:last-child { border-bottom: none; }

    .profile-photo-wrapper { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; padding-bottom: 25px; border-bottom: 2px solid #f0f0f0; }
    .profile-photo {
        width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
        border: 4px solid #354591; box-shadow: 0 4px 12px rgba(102,126,234,.3);
        flex-shrink: 0; cursor: pointer; transition: opacity .2s;
    }
    .profile-photo:hover { opacity: .85; }
    .profile-photo-info { flex: 1; min-width: 0; }
    .profile-photo-info h3 { margin: 0 0 5px; font-size: 20px; color: #2d3748; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .profile-photo-info p  { margin: 0; color: #718096; font-size: 14px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .btn-change-photo { background: #354591; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px; transition: all .3s; flex-shrink: 0; }
    .btn-change-photo:hover { background: #5568d3; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102,126,234,.4); }

    .section-title { font-size: 18px; font-weight: 700; color: #2d3748; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .section-title i { color: #354591; }

    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap: 15px; margin-bottom: 25px; }
    .info-item { background: #f7fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #354591; }
    .info-label { font-size: 12px; color: #718096; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
    .info-value { font-size: 16px; color: #2d3748; font-weight: 600; overflow: hidden; text-overflow: ellipsis; }

    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
    .status-badge.aktif      { background: #d4edda; color: #155724; }
    .status-badge.nonaktif   { background: #f8d7da; color: #721c24; }
    .status-badge.verified   { background: #d1ecf1; color: #0c5460; }
    .status-badge.unverified { background: #fff3cd; color: #856404; }

    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 14px; font-weight: 600; color: #2d3748; margin-bottom: 8px; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 15px; transition: all .3s; background: #f7fafc; }
    .form-control:focus { outline: none; border-color: #354591; background: white; box-shadow: 0 0 0 3px rgba(102,126,234,.1); }
    .form-control:disabled { background: #e2e8f0; cursor: not-allowed; }

    .btn-primary { background: #354591; color: white; border: none; padding: 14px 30px; border-radius: 10px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all .3s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102,126,234,.4); }

    .action-buttons { display: flex; gap: 15px; margin-top: 25px; flex-wrap: wrap; }

    .alert { padding: 15px 20px; margin: 0; display: flex; align-items: center; gap: 12px; font-size: 14px; flex-shrink: 0; }
    .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
    .alert-danger  { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
    .alert-info    { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; margin-bottom: 20px; }

    .face-verification-box { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 20px; }
    .face-verification-box h4 { margin: 0 0 10px; font-size: 18px; }
    .face-verification-box p  { margin: 0 0 15px; opacity: .9; font-size: 14px; }

    .wajah-registered-card { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; margin-bottom: 16px; }
    .wajah-registered-card .icon-circle { width: 38px; height: 38px; border-radius: 50%; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
    .wajah-registered-card .title { font-size: 13px; font-weight: 700; color: #065f46; }
    .wajah-registered-card .sub   { font-size: 11px; color: #059669; margin-top: 1px; }

    .wajah-status-card { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 10px; margin-bottom: 14px; font-size: 13px; }
    .wajah-status-card.pending  { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
    .wajah-status-card.approved { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
    .wajah-status-card.ditolak  { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .wajah-status-card i { margin-top: 1px; flex-shrink: 0; }
    .wajah-status-card .ws-title { font-weight: 700; margin-bottom: 2px; }
    .wajah-status-card .ws-sub   { font-size: 11px; opacity: .8; }

    .btn-capture-new { display: inline-flex; align-items: center; gap: 7px; padding: 10px 18px; background: #1d4ed8; color: white; border: none; border-radius: 9px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all .2s; margin-top: 10px; }
    .btn-capture-new:hover { background: #1e40af; color: white; transform: translateY(-1px); }

    .btn-request-wajah { width: 100%; padding: 11px 16px; background: white; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 13px; font-weight: 600; color: #374151; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-request-wajah:hover { border-color: #354591; color: #354591; }

    .password-strength { height: 4px; background: #e2e8f0; border-radius: 2px; margin-top: 8px; overflow: hidden; }
    .password-strength-bar { height: 100%; transition: all .3s; border-radius: 2px; }
    .password-strength-weak   { width: 33%; background: #dc3545; }
    .password-strength-medium { width: 66%; background: #ffc107; }
    .password-strength-strong { width: 100%; background: #28a745; }

    .input-group { position: relative; }
    .input-group .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #718096; cursor: pointer; padding: 5px; }
    .input-group .toggle-password:hover { color: #354591; }

    /* ── Modal Foto Profil ── */
    .photo-modal-overlay {
        display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,.8); z-index: 9998;
        align-items: center; justify-content: center; padding: 20px;
    }
    .photo-modal-overlay.show { display: flex; }

    .photo-modal-box {
        background: white; border-radius: 20px; width: 100%; max-width: 380px;
        overflow: hidden; box-shadow: 0 24px 60px rgba(0,0,0,.4);
        animation: popIn .3s cubic-bezier(.34,1.56,.64,1) forwards;
    }

    .photo-modal-head {
        padding: 16px 20px; display: flex; align-items: center;
        justify-content: space-between; border-bottom: 1px solid #f1f5f9;
    }
    .photo-modal-head .pmh-title { font-size: 15px; font-weight: 700; color: #1e293b; }
    .photo-modal-head .pmh-close {
        background: none; border: none; font-size: 20px; color: #94a3b8;
        cursor: pointer; line-height: 1; padding: 2px 6px; border-radius: 6px;
    }
    .photo-modal-head .pmh-close:hover { background: #f1f5f9; color: #dc3545; }

    .photo-modal-img-wrap {
        padding: 24px 20px 16px; display: flex;
        flex-direction: column; align-items: center; gap: 10px; background: #f8fafc;
    }
    .photo-modal-img-wrap img {
        width: 180px; height: 180px; border-radius: 50%; object-fit: cover;
        border: 4px solid #354591; box-shadow: 0 8px 24px rgba(53,69,145,.2);
        transition: opacity .25s;
    }

    .photo-new-label {
        display: none; padding: 4px 14px; background: #dbeafe; color: #1d4ed8;
        border-radius: 20px; font-size: 12px; font-weight: 700;
    }
    .photo-new-label.show { display: inline-block; }

    .photo-modal-footer {
        padding: 14px 20px 20px; display: flex; flex-direction: column; gap: 10px;
    }

    /* State: VIEW (default) */
    .photo-modal-footer .pm-btn-row { display: flex; gap: 10px; }
    .pm-btn {
        flex: 1; padding: 11px; border-radius: 10px; font-size: 13px; font-weight: 600;
        cursor: pointer; border: none; display: flex; align-items: center;
        justify-content: center; gap: 7px; transition: all .2s;
    }
    .pm-btn.close-btn  { background: #f1f5f9; color: #64748b; }
    .pm-btn.close-btn:hover  { background: #e2e8f0; }
    .pm-btn.change-btn { background: #354591; color: white; }
    .pm-btn.change-btn:hover { background: #5568d3; }

    /* State: PREVIEW (setelah pilih foto) */
    .pm-btn.save-btn   { background: #16a34a; color: white; display: none; }
    .pm-btn.save-btn:hover   { background: #15803d; }
    .pm-btn.save-btn.show    { display: flex; }
    .pm-btn.cancel-btn { background: #f1f5f9; color: #64748b; display: none; }
    .pm-btn.cancel-btn.show  { display: flex; }

    /* wajah modal */
    .wajah-modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
    .wajah-modal-overlay.show { display: flex; }
    .wajah-modal-box { background: white; border-radius: 20px; width: 100%; max-width: 420px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,.2); animation: popIn .3s cubic-bezier(.34,1.56,.64,1) forwards; }
    @keyframes popIn { from{opacity:0;transform:scale(.9) translateY(16px)} to{opacity:1;transform:scale(1) translateY(0)} }
    .wajah-modal-head { background: #eff6ff; padding: 18px 20px 14px; display: flex; align-items: center; gap: 12px; }
    .wajah-modal-head .icon-box { width: 40px; height: 40px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .wajah-modal-head .icon-box i { color: #1d4ed8; font-size: 16px; }
    .wajah-modal-head .mh-title { font-size: 15px; font-weight: 700; color: #1e293b; }
    .wajah-modal-head .mh-sub   { font-size: 11px; color: #64748b; }
    .wajah-modal-head .close-btn { margin-left: auto; background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer; padding: 4px; }
    .wajah-modal-body { padding: 18px 20px 20px; }
    .wajah-modal-body label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .wajah-modal-body textarea { width: 100%; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 13px; resize: vertical; box-sizing: border-box; }
    .wajah-modal-body textarea:focus { outline: none; border-color: #1d4ed8; box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
    .wajah-modal-body small { display: block; font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .wajah-modal-footer { display: flex; gap: 10px; margin-top: 16px; }
    .wajah-modal-footer .btn-cancel { flex: 1; padding: 10px; background: #f1f5f9; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; }
    .wajah-modal-footer .btn-submit { flex: 1; padding: 10px; background: #1d4ed8; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; }
    .wajah-modal-footer .btn-submit:hover { background: #1e40af; }

    @media (max-width: 768px) {
        .profile-section { padding: 20px 15px; }
        .profile-photo-wrapper { flex-direction: column; text-align: center; align-items: center; }
        .profile-photo-info { text-align: center; }
        .profile-photo-info h3, .profile-photo-info p { white-space: normal; }
        .action-buttons { flex-direction: column; }
        .btn-primary { width: 100%; justify-content: center; }
        .info-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 480px) {
        .profile-photo { width: 80px; height: 80px; }
        .section-title { font-size: 16px; }
        .btn-change-photo { width: 100%; justify-content: center; }
        .action-buttons { margin-top: 15px; }
        .btn-primary { width: 100%; padding: 12px 20px; font-size: 14px; justify-content: center; }
        .form-control { padding: 10px 12px; font-size: 14px; }
        .info-item { padding: 12px; }
        .wajah-modal-box { border-radius: 16px; max-width: 95%; }
        .photo-modal-box { border-radius: 16px; max-width: 95%; }
    }
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span></div>
    @endif

    <div class="settings-content">

        {{-- ════ Foto Profil ════ --}}
        <div class="profile-section">
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                <span>Foto Profil</span>
            </div>

            <div class="profile-photo-wrapper">
                {{-- Foto diklik → buka modal preview --}}
                <img src="{{ $karyawan && $karyawan->foto_profil ? asset('storage/'.$karyawan->foto_profil) : asset('images/default-avatar.png') }}"
                     alt="Profile Photo" class="profile-photo" id="profilePhotoMain"
                     onclick="openPhotoModal()"
                     title="Klik untuk lihat atau ganti foto">

                <div class="profile-photo-info">
                    <h3>{{ auth()->user()->nama }}</h3>
                    <p>{{ $karyawan->nip ?? 'Admin' }}</p>
                    <p style="color:#354591;font-weight:600;">
                        {{ $karyawan->jabatan->nama_jabatan ?? ucfirst(auth()->user()->role) }}
                    </p>
                </div>

                @if($karyawan)
                {{-- Form tetap ada tapi hidden, submit via JS --}}
                <form action="{{ route('settings.update-photo') }}" method="POST"
                      enctype="multipart/form-data" id="photoForm" style="display:none;">
                    @csrf
                    <input type="file" name="foto_profil" id="fotoProfilInput" accept="image/*">
                </form>
                {{-- Tombol ubah foto di luar modal juga tetap ada --}}
                <button type="button" class="btn-change-photo" onclick="openPhotoModal()">
                    <i class="fas fa-camera"></i> Ubah Foto
                </button>
                @endif
            </div>

            @if($karyawan && !$karyawan->wajah_terdaftar && !is_admin_mode())
            <div class="face-verification-box">
                <h4><i class="fas fa-exclamation-triangle"></i> Wajah Belum Terdaftar</h4>
                <p>Daftarkan wajah Anda untuk menggunakan fitur absensi dengan face recognition</p>
                <a href="{{ route('karyawan.wajah.register', $karyawan->id) }}"
                   class="btn btn-sm btn-primary">
                    Daftar Wajah
                </a>
            </div>
            @endif
        </div>

        {{-- ════ Informasi Pribadi ════ --}}
        @if($karyawan)
        {{-- ════ Personal Data (Unified) ════ --}}
        <div class="profile-section">
            <div class="section-title">
                <i class="fas fa-user-edit"></i>
                <span>Personal Data</span>
            </div>
            
            {{-- Read-only Company Info --}}
            <div class="info-grid mb-4" style="border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 25px !important;">
                <div class="info-item">
                    <div class="info-label">NIP</div>
                    <div class="info-value">{{ $karyawan->nip ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Departemen</div>
                    <div class="info-value">{{ $karyawan->departemen->nama ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jabatan</div>
                    <div class="info-value">{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ auth()->user()->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge {{ $karyawan->status }}">
                            <i class="fas {{ $karyawan->status == 'aktif' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ ucfirst($karyawan->status) }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Verifikasi Wajah</div>
                    <div class="info-value">
                        <span class="status-badge {{ $karyawan->wajah_terdaftar ? 'verified' : 'unverified' }}">
                            <i class="fas {{ $karyawan->wajah_terdaftar ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                            {{ $karyawan->wajah_terdaftar ? 'Terverifikasi' : 'Belum Verifikasi' }}
                        </span>
                    </div>
                </div>
            </div>

            <form action="{{ route('settings.update-personal') }}" method="POST">
                @csrf
                <div class="info-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="no_telepon" class="form-control" placeholder="08xxxxxxxxxx"
                               value="{{ old('no_telepon', $karyawan->no_telepon) }}">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Additional Phone Number</label>
                        <input type="text" name="no_telepon_tambahan" class="form-control" placeholder="08xxxxxxxxxx"
                               value="{{ old('no_telepon_tambahan', $karyawan->no_telepon_tambahan) }}">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Place of Birth</label>
                        <input type="text" name="tempat_lahir" class="form-control" placeholder="Contoh: Jakarta"
                               value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="tanggal_lahir" class="form-control"
                               value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Gender</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="laki-laki" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Marital Status</label>
                        <select name="status_pernikahan" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="belum_menikah" {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'belum_menikah' ? 'selected' : '' }}>Belum Menikah</option>
                            <option value="menikah" {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'menikah' ? 'selected' : '' }}>Menikah</option>
                            <option value="cerai" {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'cerai' ? 'selected' : '' }}>Cerai</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Blood Type</label>
                        <select name="golongan_darah" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(['A', 'B', 'AB', 'O'] as $gd)
                                <option value="{{ $gd }}" {{ old('golongan_darah', $karyawan->golongan_darah) == $gd ? 'selected' : '' }}>{{ $gd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Religion</label>
                        <select name="agama" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach(['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu'] as $ag)
                                <option value="{{ $ag }}" {{ old('agama', $karyawan->agama) == $ag ? 'selected' : '' }}>{{ ucfirst($ag) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ── Identity & Address ── --}}
                <div class="section-title" style="margin-top: 30px;">
                    <i class="fas fa-address-card"></i>
                    <span>Identity & Address</span>
                </div>

                <div class="info-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">NIK (16 digit)</label>
                        <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" maxlength="16"
                               value="{{ old('nik', $karyawan->nik) }}">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="kode_pos" class="form-control" placeholder="Contoh: 40264" maxlength="10"
                               value="{{ old('kode_pos', $karyawan->kode_pos) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Citizen ID Address (Alamat KTP)</label>
                    <textarea name="alamat_ktp" class="form-control" rows="2" placeholder="Alamat sesuai KTP">{{ old('alamat_ktp', $karyawan->alamat_ktp) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Residential Address (Alamat Tinggal)</label>
                    <textarea name="alamat_tinggal" class="form-control" rows="2" placeholder="Alamat tempat tinggal saat ini">{{ old('alamat_tinggal', $karyawan->alamat_tinggal) }}</textarea>
                </div>

                <div class="info-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Passport Number</label>
                        <input type="text" name="no_paspor" class="form-control" placeholder="Nomor paspor"
                               value="{{ old('no_paspor', $karyawan->no_paspor) }}">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Passport Expiry Date</label>
                        <input type="date" name="masa_berlaku_paspor" class="form-control"
                               value="{{ old('masa_berlaku_paspor', $karyawan->masa_berlaku_paspor?->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan Data Pribadi
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- ════ Template Wajah ════ --}}
        @if($karyawan && $karyawan->wajah_terdaftar && !is_admin_mode())
        <div class="profile-section">
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                <span>Template Wajah</span>
            </div>

            <div class="wajah-registered-card">
                <div class="icon-circle"><i class="fas fa-check"></i></div>
                <div>
                    <div class="title">Wajah Sudah Terdaftar</div>
                    <div class="sub">
                        Terakhir diperbarui:
                        {{ optional($wajahKaryawan?->updated_at)->format('d M Y, H:i') ?? '-' }}
                    </div>
                </div>
            </div>

            @if($wajahRequest)
                @if($wajahRequest->status === 'pending')
                <div class="wajah-status-card pending">
                    <i class="fas fa-hourglass-half"></i>
                    <div>
                        <div class="ws-title">Permohonan Sedang Diproses</div>
                        <div class="ws-sub">
                            Diajukan {{ $wajahRequest->created_at->diffForHumans() }} — Menunggu persetujuan admin
                        </div>
                    </div>
                </div>
                @elseif($wajahRequest->status === 'disetujui' && !$wajahRequest->captured_at)
                <div class="wajah-status-card approved">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <div class="ws-title">Permohonan Disetujui!</div>
                        <div class="ws-sub">Silakan lakukan capture wajah baru sekarang.</div>
                        <a href="{{ route('karyawan.wajah.capture-form') }}" class="btn-capture-new">
                            <i class="fas fa-camera"></i> Capture Wajah Baru
                        </a>
                    </div>
                </div>
                @elseif($wajahRequest->status === 'ditolak')
                <div class="wajah-status-card ditolak">
                    <i class="fas fa-times-circle"></i>
                    <div>
                        <div class="ws-title">Permohonan Ditolak</div>
                        @if($wajahRequest->catatan_admin)
                        <div class="ws-sub">Alasan: {{ $wajahRequest->catatan_admin }}</div>
                        @endif
                        <div class="ws-sub" style="margin-top:2px;">{{ $wajahRequest->reviewed_at?->diffForHumans() }}</div>
                    </div>
                </div>
                @endif
            @endif

            @php
                $canRequest = !$wajahRequest
                    || $wajahRequest->status === 'ditolak'
                    || ($wajahRequest->status === 'disetujui' && $wajahRequest->captured_at);
            @endphp

            @if($canRequest)
            <button type="button" class="btn-request-wajah"
                    onclick="document.getElementById('modalGantiWajah').classList.add('show')">
                <i class="fas fa-sync-alt"></i> Minta Ganti Template Wajah
            </button>
            @endif
        </div>
        @endif

        {{-- ════ Ubah Email ════ --}}
        <div class="profile-section">
            <div class="section-title">
                <i class="fas fa-envelope"></i>
                <span>Ubah Email</span>
            </div>
            <form action="{{ route('settings.update-email') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Saat Ini</label>
                    <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Baru</label>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan email baru" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="confirmPasswordEmail" class="form-control"
                               placeholder="Masukkan password untuk konfirmasi" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmPasswordEmail')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan Email
                    </button>
                </div>
            </form>
        </div>

        {{-- ════ Ubah Password ════ --}}
        <div class="profile-section">
            <div class="section-title">
                <i class="fas fa-lock"></i>
                <span>Ubah Password</span>
            </div>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <span>Password harus minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka</span>
            </div>
            <form action="{{ route('settings.update-password') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Password Saat Ini</label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="currentPassword" class="form-control"
                               placeholder="Masukkan password saat ini" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('currentPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="new_password" id="newPassword" class="form-control"
                               placeholder="Masukkan password baru" required oninput="checkPasswordStrength(this.value)">
                        <button type="button" class="toggle-password" onclick="togglePassword('newPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    <small id="passwordStrengthText" style="font-size:12px;color:#718096;margin-top:5px;display:block;"></small>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="new_password_confirmation" id="confirmPassword"
                               class="form-control" placeholder="Konfirmasi password baru" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-key"></i> Ubah Password
                    </button>
                </div>
            </form>
        </div>
        
        {{-- ════ Logout ════ --}}
        <div class="profile-section">
            <div class="section-title">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </div>
            <div class="alert alert-info" style="margin-bottom: 16px; background:#dbeafe; border-left-color:#3b82f6; color:#1e40af;">
                <i class="fas fa-info-circle"></i>
                <span>Keluar dari akun Anda saat ini. Pastikan semua pekerjaan sudah tersimpan.</span>
            </div>
            <button type="button" class="btn-primary" style="background: #dc3545;" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
            <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

    </div>{{-- end settings-content --}}
</div>{{-- end fullscreen-wrapper --}}

{{-- ════════════════════════════════════════════
     MODAL FOTO PROFIL
     State 1 (VIEW): tampil foto saat ini + tombol Tutup & Ganti Foto
     State 2 (PREVIEW): tampil foto baru + tombol Batal & Simpan
════════════════════════════════════════════ --}}
@if($karyawan)
<div id="photoModal" class="photo-modal-overlay"
     onclick="if(event.target===this) closePhotoModal()">
    <div class="photo-modal-box" onclick="event.stopPropagation()">

        <div class="photo-modal-head">
            <span class="pmh-title" id="photoModalTitle">Foto Profil</span>
            <button class="pmh-close" onclick="closePhotoModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="photo-modal-img-wrap">
            <img id="photoModalImg"
                 src="{{ $karyawan->foto_profil ? asset('storage/'.$karyawan->foto_profil) : asset('images/default-avatar.png') }}"
                 alt="Foto Profil">
            <span class="photo-new-label" id="photoNewLabel">
                <i class="fas fa-eye me-1"></i> Preview Foto Baru
            </span>
        </div>

        <div class="photo-modal-footer">
            {{-- Row STATE 1: VIEW --}}
            <div class="pm-btn-row" id="rowView">
                <button class="pm-btn close-btn" onclick="closePhotoModal()">
                    <i class="fas fa-times"></i> Tutup
                </button>
                <button class="pm-btn change-btn" onclick="triggerFileInput()">
                    <i class="fas fa-camera"></i> Ganti Foto
                </button>
            </div>

            {{-- Row STATE 2: PREVIEW --}}
            <div class="pm-btn-row" id="rowPreview" style="display:none;">
                <button class="pm-btn close-btn" onclick="cancelPhotoChange()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="pm-btn save-btn show" onclick="submitPhotoForm()">
                    <i class="fas fa-check"></i> Simpan Foto Ini
                </button>
            </div>
        </div>

    </div>
</div>
@endif

{{-- Modal Ganti Wajah --}}
@if($karyawan && !is_admin_mode())
<div id="modalGantiWajah" class="wajah-modal-overlay"
     onclick="if(event.target===this) document.getElementById('modalGantiWajah').classList.remove('show')">
    <div class="wajah-modal-box" onclick="event.stopPropagation()">
        <div class="wajah-modal-head">
            <div class="icon-box"><i class="fas fa-sync-alt"></i></div>
            <div>
                <div class="mh-title">Ganti Template Wajah</div>
                <div class="mh-sub">Permohonan akan direview oleh admin</div>
            </div>
            <button class="close-btn"
                    onclick="document.getElementById('modalGantiWajah').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="wajah-modal-body">
            <form action="{{ route('karyawan.wajah.request') }}" method="POST">
                @csrf
                <label>Alasan Permintaan <span style="color:#ef4444;">*</span></label>
                <textarea name="alasan" rows="4" required minlength="10" maxlength="500"
                          placeholder="Contoh: Wajah tidak terdeteksi karena perubahan penampilan..."></textarea>
                <small>Minimal 10 karakter</small>
                <div class="wajah-modal-footer">
                    <button type="button" class="btn-cancel"
                            onclick="document.getElementById('modalGantiWajah').classList.remove('show')">
                        Batal
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Kirim
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

// Logout confirmation using alert modal
function confirmLogout() {
    window.showAlert(
        'warning',
        'Konfirmasi Logout',
        'Apakah Anda yakin ingin keluar dari akun ini?',
        function() {
            document.getElementById('logoutForm').submit();
        }
    );
}
// ── Foto Profil Modal ──────────────────────────────────────

const originalSrc = document.getElementById('photoModalImg')?.src;
let pendingFile   = null;

function openPhotoModal() {
    // Reset ke state VIEW
    resetPhotoModalState();
    document.getElementById('photoModal').classList.add('show');
}

function closePhotoModal() {
    // Kalau ada preview yang belum disimpan, batalkan dulu
    if (pendingFile) cancelPhotoChange();
    document.getElementById('photoModal').classList.remove('show');
}

function triggerFileInput() {
    document.getElementById('fotoProfilInput').click();
}

function resetPhotoModalState() {
    document.getElementById('photoModalTitle').textContent = 'Foto Profil';
    document.getElementById('photoModalImg').src           = originalSrc;
    document.getElementById('photoNewLabel').classList.remove('show');
    document.getElementById('rowView').style.display       = '';
    document.getElementById('rowPreview').style.display    = 'none';
    pendingFile = null;
}

function cancelPhotoChange() {
    // Kosongkan file input supaya tidak ikut submit
    document.getElementById('fotoProfilInput').value = '';
    pendingFile = null;
    // Kembalikan foto modal DAN foto di header ke foto asli
    document.getElementById('photoModalImg').src    = originalSrc;
    document.getElementById('profilePhotoMain').src = originalSrc;
    document.getElementById('photoModalTitle').textContent = 'Foto Profil';
    document.getElementById('photoNewLabel').classList.remove('show');
    document.getElementById('rowView').style.display    = '';
    document.getElementById('rowPreview').style.display = 'none';
}

function submitPhotoForm() {
    if (!pendingFile) return;
    document.getElementById('photoForm').submit();
}

// Saat user pilih file → masuk state PREVIEW
@if($karyawan)
document.getElementById('fotoProfilInput').addEventListener('change', function (e) {
    if (!e.target.files || !e.target.files[0]) return;

    pendingFile = e.target.files[0];
    const reader = new FileReader();

    reader.onload = function (ev) {
        const img = document.getElementById('photoModalImg');
        img.style.opacity = '0.5';
        setTimeout(() => {
            img.src = ev.target.result;
            img.style.opacity = '1';
        }, 150);

        // Update foto kecil di header (preview sebelum save)
        document.getElementById('profilePhotoMain').src = ev.target.result;

        // Ganti ke state PREVIEW
        document.getElementById('photoModalTitle').textContent = 'Preview Foto Baru';
        document.getElementById('photoNewLabel').classList.add('show');
        document.getElementById('rowView').style.display    = 'none';
        document.getElementById('rowPreview').style.display = '';

        // Pastikan modal terbuka
        document.getElementById('photoModal').classList.add('show');
    };

    reader.readAsDataURL(pendingFile);
});
@endif

// ── Toggle Password ────────────────────────────────────────

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon  = event.target.closest('button').querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// ── Password Strength ──────────────────────────────────────

function checkPasswordStrength(password) {
    const bar  = document.getElementById('passwordStrengthBar');
    const text = document.getElementById('passwordStrengthText');
    let strength = 0;
    if (password.length >= 8)       strength++;
    if (password.match(/[a-z]+/))   strength++;
    if (password.match(/[A-Z]+/))   strength++;
    if (password.match(/[0-9]+/))   strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    bar.className = 'password-strength-bar';
    if (strength < 3) {
        bar.classList.add('password-strength-weak');
        text.textContent = '❌ Password lemah';
        text.style.color = '#dc3545';
    } else if (strength < 4) {
        bar.classList.add('password-strength-medium');
        text.textContent = '⚠️ Password sedang';
        text.style.color = '#ffc107';
    } else {
        bar.classList.add('password-strength-strong');
        text.textContent = '✅ Password kuat';
        text.style.color = '#28a745';
    }
}

// ── Touch feedback ─────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-primary, .btn-change-photo').forEach(btn => {
        btn.addEventListener('touchstart',  () => btn.style.transform = 'scale(0.98)');
        btn.addEventListener('touchend',    () => btn.style.transform = '');
    });
});
</script>
@endpush
