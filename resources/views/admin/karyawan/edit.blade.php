{{-- resources/views/admin/karyawan/edit.blade.php --}}

<div class="modal fade" id="ubahKaryawan{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header border-0 pb-0" style="background:#fffbeb;">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:36px;height:36px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user-edit" style="color:#d97706;font-size:15px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Ubah Data Karyawan</h6>
                        <small class="text-muted" style="font-size:11px;">{{ $item->user->nama ?? '-' }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.karyawan.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            NIP <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nip" class="form-control" style="font-size:13px;"
                            value="{{ $item->nip }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Departemen <span class="text-danger">*</span>
                        </label>
                        <select name="departemen_id" class="form-select" style="font-size:13px;" required>
                            <option value="" disabled>Pilih departemen...</option>
                            @foreach ($departemen as $list)
                                <option value="{{ $list->id }}"
                                    {{ $item->departemen_id == $list->id ? 'selected' : '' }}>
                                    {{ $list->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Jabatan <span class="text-danger">*</span>
                        </label>
                        <select name="jabatan_id" class="form-select" style="font-size:13px;" required>
                            <option value="" disabled>Pilih jabatan...</option>
                            @foreach ($jabatan as $list)
                                <option value="{{ $list->id }}"
                                    {{ $item->jabatan_id == $list->id ? 'selected' : '' }}>
                                    {{ $list->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" style="font-size:13px;"
                            value="{{ $item->no_telepon }}" maxlength="20">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Alamat</label>
                        <textarea name="alamat" class="form-control" style="font-size:13px;" rows="2"
                            value="{{ $item->alamat }}"></textarea>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" class="form-select" style="font-size:13px;" required>
                            <option value="aktif" {{ $item->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $item->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif
                            </option>
                        </select>
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
