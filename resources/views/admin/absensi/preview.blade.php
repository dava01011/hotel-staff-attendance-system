@extends('admin.layouts.app')

@section('title', 'Preview Laporan Absensi')

@push('styles')
<style>
    .preview-container {
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
    }

    .preview-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .preview-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
    }

    .preview-actions {
        display: flex;
        gap: 10px;
    }

    .btn-preview {
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-preview:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .btn-download {
        background: #16a34a;
        color: white;
    }

    .btn-download:hover {
        background: #15803d;
        color: white;
    }

    .btn-print {
        background: #2563eb;
        color: white;
    }

    .btn-print:hover {
        background: #1d4ed8;
        color: white;
    }

    .btn-back {
        background: #e2e8f0;
        color: #334155;
    }

    .btn-back:hover {
        background: #cbd5e1;
        color: #1e293b;
    }

    .pdf-frame {
        width: 100%;
        height: calc(100vh - 180px);
        min-height: 600px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .filter-info {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 12px;
    }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Preview Laporan Absensi</h4>
        <small class="text-muted">Tinjau laporan sebelum mengunduh atau mencetak</small>
    </div>
</div>

<div class="preview-container">
    <div class="preview-toolbar">
        <div class="preview-title">
            <i class="fas fa-file-pdf text-danger me-2"></i>
            Laporan Absensi
        </div>
        <div class="preview-actions">
            <a href="{{ route('admin.absensi.export-pdf', request()->query()) }}" class="btn-preview btn-download">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <button type="button" class="btn-preview btn-print" onclick="printPDF()">
                <i class="fas fa-print"></i> Cetak
            </button>
            <a href="{{ route('admin.absensi.index') }}" class="btn-preview btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="filter-info">
        <i class="fas fa-filter me-1"></i>
        @php
            $activeFilters = [];
            if (request('search')) $activeFilters[] = 'Pencarian: ' . request('search');
            if (request('status') && request('status') !== 'all') $activeFilters[] = 'Status: ' . ucfirst(request('status'));
            if (request('date_from')) $activeFilters[] = 'Dari: ' . request('date_from');
            if (request('date_to')) $activeFilters[] = 'Sampai: ' . request('date_to');
        @endphp
        {{ count($activeFilters) ? implode(' · ', $activeFilters) : 'Semua data' }}
    </div>

    <iframe id="pdfPreview" class="pdf-frame" src="{{ route('admin.absensi.preview-pdf', request()->query()) }}"></iframe>
</div>

@endsection

@push('scripts')
<script>
    function printPDF() {
        const iframe = document.getElementById('pdfPreview');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } else {
            window.open('{{ route("admin.absensi.preview-pdf", request()->query()) }}', '_blank')?.focus();
        }
    }
</script>
@endpush