<div class="modal fade" id="tambahJabatan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-plus" style="color:#1d4ed8;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Tambah Jabatan</h6>
                        <small class="text-muted" style="font-size:11px;">Tambah jabatan & struktur gaji baru</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.jabatan.store') }}" method="POST">
                @csrf

                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Nama Jabatan <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_jabatan"
                               class="form-control" style="font-size:13px;"
                               placeholder="Nama jabatan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Tipe Gaji <span class="text-danger">*</span>
                        </label>
                        <select name="tipe_gaji" class="form-select" style="font-size:13px;" required>
                            <option value="" disabled selected>Pilih tipe...</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="harian">Harian</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Jatah Cuti Bulanan (Hari) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="jatah_cuti_bulanan" step="0.5"
                               class="form-control" style="font-size:13px;"
                               placeholder="Contoh: 1.5" value="0" min="0" required>
                        <small class="text-muted" style="font-size:11px;">Jatah cuti ini akan bertambah otomatis tiap bulan</small>
                    </div>

                    <div class="row g-3 mb-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Gaji Pokok <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" style="font-size:12px;color:#6c757d;">Rp</span>
                                <input type="number" name="gaji_pokok"
                                       class="form-control" style="font-size:13px;"
                                       placeholder="0" min="0" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Gaji Harian <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" style="font-size:12px;color:#6c757d;">Rp</span>
                                <input type="number" name="gaji_harian"
                                       class="form-control" style="font-size:13px;"
                                       placeholder="0" min="0" required>
                            </div>
                        </div>
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
