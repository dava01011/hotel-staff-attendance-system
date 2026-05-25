<div class="modal fade" id="tambahShift" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-plus" style="color:#1d4ed8;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Tambah Shift</h6>
                        <small class="text-muted" style="font-size:11px;">Tambah data shift baru</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.shift.store') }}" method="POST">
                @csrf

                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Kode <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kode"
                               class="form-control" style="font-size:13px;"
                               placeholder="Contoh: Pagi, Sore, Malam" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Jam Masuk <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="jam_masuk"
                                   class="form-control" style="font-size:13px;" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Jam Pulang <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="jam_pulang"
                                   class="form-control" style="font-size:13px;" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Toleransi Keterlambatan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="toleransi_menit"
                                   class="form-control" style="font-size:13px;"
                                   placeholder="0" min="0" required>
                            <span class="input-group-text" style="font-size:12px;color:#6c757d;">menit</span>
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">Lintas Hari</label>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox"
                                       name="lintas_hari" id="lintasHariCreate" value="1">
                                <label class="form-check-label" for="lintasHariCreate"
                                       style="font-size:13px;">
                                    Shift melewati jam 00:00
                                </label>
                            </div>
                        </div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                            Aktifkan jika jam pulang berada di hari berikutnya
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
