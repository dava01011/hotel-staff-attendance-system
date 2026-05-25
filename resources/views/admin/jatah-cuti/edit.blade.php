{{-- resources/views/admin/jatah-cuti/edit.blade.php --}}

<div class="modal fade" id="ubahJatah{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#fffbeb;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-edit" style="color:#d97706;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Ubah Jatah Cuti</h6>
                        <small class="text-muted" style="font-size:11px;">
                            {{ $item->karyawan->user->nama ?? '-' }} — {{ $item->tahun }}
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.jatah-cuti.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body pt-3">

                    {{-- Karyawan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Karyawan <span class="text-danger">*</span>
                        </label>
                        <select name="karyawan_id" class="form-select" style="font-size:13px;" required>
                            <option value="" disabled>Pilih karyawan...</option>
                            @foreach ($karyawan as $list)
                                <option value="{{ $list->id }}"
                                    {{ $item->karyawan_id == $list->id ? 'selected' : '' }}>
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
                               value="{{ $item->tahun }}"
                               min="2000" max="2100" required>
                    </div>

                    {{-- Jatah Awal + Jatah Saat Ini side by side --}}
                    <div class="row g-3 mb-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Jatah Awal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="jatah_awal" step="0.5"
                                       class="form-control" style="font-size:13px;"
                                       value="{{ $item->jatah_awal }}"
                                       min="0" required>
                                <span class="input-group-text" style="font-size:12px;color:#6c757d;">hari</span>
                            </div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                Jatah saat reset Januari
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Jatah Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="jatah" step="0.5"
                                       class="form-control" style="font-size:13px;"
                                       value="{{ $item->jatah }}"
                                       min="0" required>
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
                    <button type="submit" class="btn btn-sm px-4 fw-semibold"
                            style="background:#fbbf24;color:#1e293b;border:none;">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
