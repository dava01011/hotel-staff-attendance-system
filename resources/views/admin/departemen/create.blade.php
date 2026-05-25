<div class="modal fade" id="tambahDepartemen" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-plus" style="color:#1d4ed8;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Tambah Departemen</h6>
                        <small class="text-muted" style="font-size:11px;">Tambah departemen baru</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.departemen.store') }}" method="POST">
                @csrf

                <div class="modal-body pt-3">
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Nama Departemen <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama"
                               class="form-control" style="font-size:13px;"
                               placeholder="Masukkan nama departemen..." required>
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
