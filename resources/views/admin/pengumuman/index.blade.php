@extends('admin.layouts.app')

@section('title', 'Manajemen Pengumuman')

@push('styles')
<style>
    .announcement-card {
        border-left: 4px solid #0d6efd;
        transition: transform 0.2s;
    }
    .announcement-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .announcement-card.global {
        border-left-color: #198754;
    }
    .announcement-card.departemen {
        border-left-color: #0d6efd;
    }
    .badge-global {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    .badge-departemen {
        background-color: #cfe2ff;
        color: #084298;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .action-btn.edit { background: #fff3cd; color: #d97706; }
    .action-btn.edit:hover { background: #fbbf24; color: white; }
    .action-btn.delete { background: #fee2e2; color: #dc2626; }
    .action-btn.delete:hover { background: #ef4444; color: white; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pengumuman</h4>
        <small class="text-muted">Kelola pengumuman untuk karyawan</small>
    </div>
    <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus"></i> Buat Pengumuman
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    @forelse($pengumuman as $p)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm border-0 announcement-card {{ $p->tipe }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge {{ $p->tipe === 'global' ? 'badge-global' : 'badge-departemen' }} rounded-pill px-3 py-2">
                        <i class="fas {{ $p->tipe === 'global' ? 'fa-globe' : 'fa-building' }} me-1"></i>
                        {{ $p->tipe === 'global' ? 'Global' : ($p->departemen->nama ?? 'Departemen') }}
                    </span>
                    
                    <div class="d-flex gap-1">
                        <button class="action-btn edit" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" data-bs-toggle="modal" data-bs-target="#modalHapus{{ $p->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <h5 class="card-title fw-bold mt-3 mb-2 text-truncate" title="{{ $p->judul }}">{{ $p->judul }}</h5>
                <p class="card-text text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $p->konten }}
                </p>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 12px;">
                    <div>
                        <div><i class="fas fa-user me-1"></i> {{ $p->pembuat->nama ?? 'Sistem' }}</div>
                        <div class="mt-1" style="font-size: 11px; color: #6b7280;">
                            <i class="fas fa-briefcase me-1"></i> {{ $p->pembuat->karyawan->jabatan->nama_jabatan ?? '-' }}
                            <span class="mx-1">•</span>
                            <i class="fas fa-building me-1"></i> {{ $p->pembuat->karyawan->departemen->nama ?? '-' }}
                        </div>
                    </div>
                    <span><i class="fas fa-clock me-1"></i> {{ $p->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Pengumuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pengumuman.update', $p->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Pengumuman <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" value="{{ $p->judul }}" required>
                        </div>
                        
                        @if(in_array(auth()->user()->role, ['super_admin', 'gm']))
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Pengumuman <span class="text-danger">*</span></label>
                            <select class="form-select tipe-select" name="tipe" required data-target="#editDepartemen{{ $p->id }}">
                                <option value="global" {{ $p->tipe === 'global' ? 'selected' : '' }}>Global (Semua Karyawan)</option>
                                <option value="departemen" {{ $p->tipe === 'departemen' ? 'selected' : '' }}>Spesifik Departemen</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="editDepartemen{{ $p->id }}" style="display: {{ $p->tipe === 'departemen' ? 'block' : 'none' }};">
                            <label class="form-label fw-semibold">Pilih Departemen <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen_id">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departemen as $d)
                                <option value="{{ $d->id }}" {{ $p->departemen_id == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Pengumuman ini hanya akan dilihat oleh karyawan di departemen Anda.
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Isi Pengumuman <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="konten" rows="5" required>{{ $p->konten }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div class="modal fade" id="modalHapus{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <h5 class="mb-2">Hapus Pengumuman?</h5>
                    <p class="text-muted small mb-4">Pengumuman "{{ $p->judul }}" akan dihapus permanen.</p>
                    <form action="{{ route('admin.pengumuman.destroy', $p->id) }}" method="POST" class="d-flex gap-2 justify-content-center">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="fas fa-bullhorn fa-3x text-muted mb-3" style="opacity: 0.2"></i>
            <h5>Belum Ada Pengumuman</h5>
            <p class="text-muted">Klik tombol "Buat Pengumuman" untuk membuat pengumuman pertama Anda.</p>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Buat Pengumuman Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pengumuman.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul" placeholder="Contoh: Info Libur Lebaran" required>
                    </div>
                    
                    @if(in_array(auth()->user()->role, ['super_admin', 'gm']))
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Pengumuman <span class="text-danger">*</span></label>
                        <select class="form-select tipe-select" name="tipe" required data-target="#tambahDepartemen">
                            <option value="global">Global (Semua Karyawan)</option>
                            <option value="departemen">Spesifik Departemen</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="tambahDepartemen" style="display: none;">
                        <label class="form-label fw-semibold">Pilih Departemen <span class="text-danger">*</span></label>
                        <select class="form-select" name="departemen_id">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemen as $d)
                            <option value="{{ $d->id }}">{{ $d->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Pengumuman ini hanya akan dilihat oleh karyawan di departemen Anda.
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Pengumuman <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="konten" rows="5" placeholder="Ketik isi pengumuman di sini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Sebarkan Pengumuman</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipeSelects = document.querySelectorAll('.tipe-select');
        tipeSelects.forEach(select => {
            select.addEventListener('change', function() {
                const targetId = this.getAttribute('data-target');
                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    if (this.value === 'departemen') {
                        targetEl.style.display = 'block';
                        targetEl.querySelector('select').setAttribute('required', 'required');
                    } else {
                        targetEl.style.display = 'none';
                        targetEl.querySelector('select').removeAttribute('required');
                    }
                }
            });
        });
    });
</script>
@endpush
