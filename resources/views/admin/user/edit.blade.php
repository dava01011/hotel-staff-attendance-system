<div class="modal fade" id="ubahUser{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0" style="background:#fffbeb;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user-edit" style="color:#d97706;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Edit User</h6>
                        <small class="text-muted" style="font-size:11px;">{{ $item->nama }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.user.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" style="font-size:13px;"
                            value="{{ $item->nama }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Email</label>
                        <input type="email" name="email" class="form-control" style="font-size:13px;"
                            value="{{ $item->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Password Baru
                            <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" name="password" class="form-control" style="font-size:13px;"
                                placeholder="Kosongkan jika tidak diubah">
                            <span class="password-toggle"><i class="far fa-eye"></i></span>
                        </div>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            Jika user lupa password, Super Admin dapat mengatur ulang di sini.
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Role</label>
                        <select name="role" class="form-select" style="font-size:13px;" required>
                            <option value="super_admin" {{ $item->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ $item->role == 'admin' ? 'selected' : '' }}>Admin Departemen</option>
                            <option value="gm" {{ $item->role == 'gm' ? 'selected' : '' }}>General Manager</option>
                            <option value="karyawan" {{ $item->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Status</label>
                        <select name="status" class="form-select" style="font-size:13px;">
                            <option value="aktif" {{ $item->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $item->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
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