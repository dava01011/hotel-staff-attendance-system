{{-- resources/views/admin/jatah-cuti/create.blade.php --}}

<div class="modal fade" id="tambahJatah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-plus" style="color:#1d4ed8;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Tambah Jatah Cuti</h6>
                        <small class="text-muted" style="font-size:11px;">Input manual jatah cuti karyawan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.jatah-cuti.store') }}" method="POST">
                @csrf

                <div class="modal-body pt-3">

                    {{-- Karyawan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Karyawan <span class="text-danger">*</span>
                        </label>
                        <select name="karyawan_id" class="form-select" style="font-size:13px;" required>
                            <option value="" disabled selected>Pilih karyawan...</option>
                            @foreach ($karyawan as $list)
                                <option value="{{ $list->id }}">
                                    {{ $list->user->nama ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tahun --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Tahun <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="tahun"
                               class="form-control" style="font-size:13px;"
                               value="{{ now()->year }}"
                               min="2000" max="2100" required>
                    </div>

                    {{-- Jatah Awal + Jatah --}}
                    <div class="row g-3 mb-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Jatah Awal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="jatah_awal" step="0.5"
                                       class="form-control" style="font-size:13px;"
                                       value="6" min="0" required>
                                <span class="input-group-text" style="font-size:12px;color:#6c757d;">hari</span>
                            </div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                Default reset Januari
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Jatah Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="jatah" step="0.5"
                                       class="form-control" style="font-size:13px;"
                                       value="6" min="0" required>
                                <span class="input-group-text" style="font-size:12px;color:#6c757d;">hari</span>
                            </div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                Jatah aktif sekarang
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
