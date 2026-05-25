{{-- resources/views/admin/karyawan/create.blade.php --}}

<div class="modal fade" id="tambahKaryawan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user-plus" style="color:#1d4ed8;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Tambah Karyawan</h6>
                        <small class="text-muted" style="font-size:11px;">Tambah data karyawan baru</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.karyawan.store') }}" method="POST">
                @csrf

                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Nama Karyawan <span class="text-danger">*</span>
                        </label>
                        <select name="user_id" class="form-select" style="font-size:13px;" required>
                            <option value="" disabled selected>Pilih karyawan...</option>
                            @foreach ($users as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            NIP <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nip"
                               class="form-control" style="font-size:13px;"
                               placeholder="Masukkan NIP" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Departemen <span class="text-danger">*</span>
                        </label>
                        <select name="departemen_id" class="form-select" style="font-size:13px;" required>
                            <option value="" disabled selected>Pilih departemen...</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Jabatan <span class="text-danger">*</span>
                        </label>
                        <select name="jabatan_id" class="form-select" style="font-size:13px;" required>
                            <option value="" disabled selected>Pilih jabatan...</option>
                            @foreach ($jabatan as $j)
                                <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
    <label class="form-label fw-semibold" style="font-size:13px;">No. Telepon</label>
    <input type="text" name="no_telepon" class="form-control" style="font-size:13px;"
           placeholder="08xxxxxxxxxx" maxlength="20">
</div>

<div class="mb-3">
    <label class="form-label fw-semibold" style="font-size:13px;">Alamat</label>
    <textarea name="alamat" class="form-control" style="font-size:13px;" rows="2"
              placeholder="Alamat lengkap karyawan"></textarea>
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
