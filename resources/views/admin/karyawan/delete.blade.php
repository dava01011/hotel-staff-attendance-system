    {{-- resources/views/admin/karyawan/delete.blade.php --}}

    <div class="modal fade" id="hapusKaryawan{{ $item->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content shadow-lg border-0">

                <div class="modal-header border-0 pb-0" style="background:#fef2f2;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-trash" style="color:#dc2626;font-size:15px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size:14px;">Hapus Karyawan</h6>
                            <small class="text-muted" style="font-size:11px;">Tindakan tidak bisa dibatalkan</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.karyawan.destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body text-center py-4">
                        <p class="mb-1" style="font-size:13px;">
                            Yakin ingin menghapus karyawan:
                        </p>
                        <div class="fw-bold" style="font-size:14px;color:#1e293b;">
                            {{ $item->user->nama ?? $item->nama ?? '-' }}
                        </div>
                        <p class="text-muted mt-2" style="font-size:12px;">
                            Data akan dihapus permanen dan tidak bisa dikembalikan.
                        </p>
                    </div>

                    <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm px-4 fw-semibold">
                            <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
