@extends('admin.layouts.app')

@section('title', 'Generate Hari Libur Nasional')

@push('styles')
<style>
    .generate-card {
        border: 2px solid #e9ecef; border-radius: 14px; padding: 24px;
        transition: all 0.2s; background: white; cursor: pointer;
    }
    .generate-card:hover  { border-color: #0d6efd; box-shadow: 0 4px 16px rgba(13,110,253,0.1); }
    .generate-card.active { border-color: #0d6efd; background: #eff6ff; }

    .gen-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; margin-bottom: 14px;
    }

    .preview-table { font-size: 12px; }
    .preview-table th { font-size: 11px; font-weight: 700; color: #64748b; }
    .preview-table .day-col { color: #6c757d; }

    #previewSection { display: none; }
    #previewSection.show { display: block; }

    .loading-spinner {
        display: none; text-align: center; padding: 30px;
    }
    .loading-spinner.show { display: block; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.hari-libur-template.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1">Generate Hari Libur Nasional</h4>
        <small class="text-muted">Buat hari libur nasional dari template yang sudah ada</small>
    </div>
</div>

<div class="row g-4">
    {{-- Left: Form --}}
    <div class="col-md-5">

        {{-- Single year generate --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header border-0 pt-4 pb-0 px-4" style="background:white;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-magic" style="color:#1d4ed8;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Generate 1 Tahun</h6>
                        <small class="text-muted" style="font-size:11px;">Generate hari libur untuk tahun tertentu</small>
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.hari-libur-template.generate', ':tahun') }}"
                  method="POST" id="singleForm">
                @csrf
                <div class="card-body pt-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">
                        Tahun <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-2">
                        <input type="number" name="tahun" id="singleTahun"
                               class="form-control" style="font-size:13px;"
                               min="2024" max="2100"
                               value="{{ $tahunSekarang }}" required>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                onclick="previewTahun(document.getElementById('singleTahun').value)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text" style="font-size:11px;margin-top:6px;">
                        Klik 👁 untuk preview sebelum generate.
                    </div>
                </div>
                <div class="card-footer border-0 bg-white d-flex justify-content-end pb-4 px-4">
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold"
                            onclick="setSingleAction()">
                        <i class="fas fa-magic me-1"></i> Generate
                    </button>
                </div>
            </form>
        </div>

        {{-- Bulk generate --}}
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pt-4 pb-0 px-4" style="background:white;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#d1fae5;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-layer-group" style="color:#065f46;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Bulk Generate</h6>
                        <small class="text-muted" style="font-size:11px;">Generate untuk range beberapa tahun sekaligus</small>
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.hari-libur-template.generate-bulk') }}" method="POST">
                @csrf
                <div class="card-body pt-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Dari Tahun <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="tahun_mulai" class="form-control"
                                   style="font-size:13px;" min="2024" max="2100"
                                   value="{{ $tahunSekarang }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Sampai Tahun <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="tahun_selesai" class="form-control"
                                   style="font-size:13px;" min="2024" max="2100"
                                   value="{{ $tahunSekarang + 1 }}" required>
                        </div>
                    </div>
                    <div class="alert border-0 mt-3 mb-0 p-2"
                         style="background:#fef9c3;font-size:12px;border-radius:8px;">
                        <i class="fas fa-exclamation-triangle me-1" style="color:#d97706;"></i>
                        Hari libur yang sudah ada tidak akan diduplikasi (unique by tanggal).
                    </div>
                </div>
                <div class="card-footer border-0 bg-white d-flex justify-content-end pb-4 px-4">
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-semibold">
                        <i class="fas fa-layer-group me-1"></i> Bulk Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Right: Preview --}}
    <div class="col-md-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 pt-4 pb-0 px-4" style="background:white;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f3e8ff;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-eye" style="color:#7c3aed;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Preview</h6>
                        <small class="text-muted" style="font-size:11px;">
                            Hari libur yang akan di-generate (sebelum simpan)
                        </small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- Empty state --}}
                <div id="previewEmpty" class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-alt fa-3x mb-3" style="opacity:0.25;"></i>
                    <p class="mb-0 fw-medium" style="font-size:13px;">
                        Masukkan tahun dan klik 👁 untuk preview
                    </p>
                </div>

                {{-- Loading --}}
                <div class="loading-spinner" id="previewLoading">
                    <div class="spinner-border text-primary" style="width:2rem;height:2rem;"></div>
                    <div class="mt-2 text-muted" style="font-size:13px;">Memuat preview...</div>
                </div>

                {{-- Preview content --}}
                <div id="previewSection">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="fw-bold" style="font-size:14px;" id="previewTitle">-</span>
                            <span class="badge bg-primary ms-2" id="previewCount">0</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle preview-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function setSingleAction() {
        const tahun = document.getElementById('singleTahun').value;
        const form  = document.getElementById('singleForm');
        form.action = form.action.replace(':tahun', tahun);
    }

    function previewTahun(tahun) {
        if (!tahun) return;

        document.getElementById('previewEmpty').style.display   = 'none';
        document.getElementById('previewSection').classList.remove('show');
        document.getElementById('previewLoading').classList.add('show');

        fetch(`{{ route('api.hari-libur-preview', ':tahun') }}`.replace(':tahun', tahun))
            .then(r => r.json())
            .then(data => {
                document.getElementById('previewLoading').classList.remove('show');
                document.getElementById('previewSection').classList.add('show');

                document.getElementById('previewTitle').textContent = `Hari Libur Tahun ${data.tahun}`;
                document.getElementById('previewCount').textContent = `${data.total} hari`;

                const tbody = document.getElementById('previewBody');
                tbody.innerHTML = '';

                if (!data.holidays || data.holidays.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-muted">
                        Tidak ada template dynamic untuk tahun ini.</td></tr>`;
                    return;
                }

                data.holidays.forEach((h, i) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="ps-3">${i+1}</td>
                        <td><span style="font-size:12px;font-weight:600;color:#1d4ed8;">${h.tanggal}</span></td>
                        <td class="day-col">${h.hari}</td>
                        <td style="font-weight:600;color:#2d3748;">${h.nama}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(() => {
                document.getElementById('previewLoading').classList.remove('show');
                document.getElementById('previewEmpty').style.display = 'block';
                document.getElementById('previewEmpty').innerHTML = `
                    <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                    <p class="text-muted" style="font-size:13px;">Gagal memuat preview. Coba lagi.</p>
                `;
            });
    }

    // Auto preview on load
    previewTahun({{ $tahunSekarang }});
</script>
@endpush
