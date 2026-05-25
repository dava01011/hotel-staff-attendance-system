@extends('admin.layouts.app')

@section('title', 'Template Hari Libur')

@push('styles')
<style>
    .tipe-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 700; letter-spacing: 0.3px;
    }
    .tipe-badge.fixed   { background: #dbeafe; color: #1d4ed8; }
    .tipe-badge.dynamic { background: #fef3c7; color: #92400e; }

    .btn-sm { padding: 6px 12px; transition: all 0.2s; }
    .btn-sm:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .table tbody tr { transition: background-color 0.2s; }

    .action-group { display: flex; gap: 6px; justify-content: center; }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Template Hari Libur</h4>
        <small class="text-muted">Template untuk generate hari libur nasional secara otomatis</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.hari-libur-template.generate-form') }}"
           class="btn btn-sm btn-outline-success d-flex align-items-center gap-2">
            <i class="fas fa-magic"></i> Generate ke Tahun
        </a>
        <a href="{{ route('admin.hari-libur-template.create') }}"
           class="btn btn-sm btn-primary d-flex align-items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Template
        </a>
    </div>
</div>

{{-- Info box --}}
<div class="alert border-0 mb-3 d-flex align-items-start gap-3"
     style="background:#eff6ff;border-left:4px solid #3b82f6 !important;border-left-style:solid;">
    <i class="fas fa-info-circle mt-1" style="color:#1d4ed8;"></i>
    <div style="font-size:13px;">
        <strong>Template Fixed</strong> = tanggal tetap tiap tahun (contoh: 17 Agustus selalu sama).<br>
        <strong>Template Dynamic</strong> = tanggal berubah tiap tahun (contoh: Lebaran, Nyepi).
        Perlu input tanggal per tahun.
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Nama Template</th>
                        <th>Tipe</th>
                        <th>Detail</th>
                        <th>Keterangan</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $item)
                        <tr>
                            <td class="ps-4">{{ $loop->iteration + ($templates->currentPage() - 1) * $templates->perPage() }}</td>
                            <td>
                                <div class="fw-semibold" style="color:#2d3748;font-size:14px;">
                                    {{ $item->nama }}
                                </div>
                            </td>
                            <td>
                                <span class="tipe-badge {{ $item->tipe }}">
                                    @if($item->tipe === 'fixed')
                                        <i class="fas fa-thumbtack"></i> Fixed
                                    @else
                                        <i class="fas fa-sync-alt"></i> Dynamic
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($item->tipe === 'fixed')
                                    <span class="text-muted" style="font-size:13px;">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        Setiap tgl {{ $item->hari }} bulan {{ \Carbon\Carbon::create()->month($item->bulan)->locale('id')->isoFormat('MMMM') }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:13px;">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Tahun {{ $item->tahun_mulai }} &ndash; {{ $item->tahun_selesai }}
                                        ({{ count((array)$item->tanggal_per_tahun) }} entri)
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:13px;">
                                    {{ $item->keterangan ?: '-' }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <div class="action-group">
                                    <a href="{{ route('admin.hari-libur-template.edit', $item->id) }}"
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" title="Hapus"
                                        data-bs-toggle="modal"
                                        data-bs-target="#hapusTemplate{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3" style="opacity:0.3;"></i>
                                    <p class="mb-0 fw-medium">Belum ada template hari libur</p>
                                    <small>Klik "Tambah Template" untuk memulai</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="px-4 py-3 border-top">{{ $templates->links() }}</div>
        @endif
    </div>
</div>

{{-- Delete modals --}}
@foreach($templates as $item)
<div class="modal fade" id="hapusTemplate{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0" style="background:#fef2f2;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-trash" style="color:#dc2626;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Hapus Template</h6>
                        <small class="text-muted" style="font-size:11px;">Tindakan tidak bisa dibatalkan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.hari-libur-template.destroy', $item->id) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body text-center py-4">
                    <p style="font-size:13px;" class="mb-1">Yakin hapus template:</p>
                    <div class="fw-bold" style="font-size:14px;color:#1e293b;">{{ $item->nama }}</div>
                    <p class="text-muted mt-2" style="font-size:12px;">
                        Hari libur yang sudah di-generate tidak akan ikut terhapus.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-semibold">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `position:fixed;top:20px;right:20px;padding:15px 25px;
            background:${type==='success'?'#28a745':'#dc3545'};color:white;border-radius:8px;
            font-weight:600;font-size:14px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.15);
            animation:slideInRight 0.3s ease-out;display:flex;align-items:center;gap:10px;max-width:420px;`;
        t.innerHTML = `<i class="fas fa-check-circle"></i><span>${msg}</span>`;
        document.body.appendChild(t);
        setTimeout(()=>{t.style.opacity='0';t.style.transition='opacity 0.3s';setTimeout(()=>t.remove(),300);},3500);
    }
    @if(session('success')) showToast(`{!! session('success') !!}`, 'success'); @endif
    @if(session('error'))   showToast(`{!! session('error') !!}`, 'error');   @endif
</script>
@endpush
