<div class="modal fade" id="ubahShift{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#fffbeb;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-edit" style="color:#d97706;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Ubah Shift</h6>
                        <small class="text-muted" style="font-size:11px;">
                            {{ $item->kode }} ({{ substr($item->jam_masuk, 0, 5) }} – {{ substr($item->jam_pulang, 0, 5) }})
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.shift.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Kode <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kode"
                               class="form-control" style="font-size:13px;"
                               value="{{ $item->kode }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Jam Masuk <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="jam_masuk"
                                   class="form-control" style="font-size:13px;"
                                   value="{{ substr($item->jam_masuk, 0, 5) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Jam Pulang <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="jam_pulang"
                                   class="form-control" style="font-size:13px;"
                                   value="{{ substr($item->jam_pulang, 0, 5) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Toleransi Keterlambatan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="toleransi_menit"
                                   class="form-control" style="font-size:13px;"
                                   value="{{ $item->toleransi_menit }}" min="0" required>
                            <span class="input-group-text" style="font-size:12px;color:#6c757d;">menit</span>
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">Lintas Hari</label>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox"
                                       name="lintas_hari"
                                       id="lintasHari{{ $item->id }}"
                                       value="1"
                                       {{ $item->lintas_hari ? 'checked' : '' }}>
                                <label class="form-check-label"
                                       for="lintasHari{{ $item->id }}"
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
                    <button type="submit" class="btn btn-sm px-4 fw-semibold"
                            style="background:#fbbf24;color:#1e293b;border:none;">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
