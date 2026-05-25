{{-- File: resources/views/absensi/detect_absent_form.blade.php --}}

@extends('admin.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-search"></i> Detect Absent (Tidak Hadir)
                    </h5>
                </div>

                <div class="card-body">
                    {{-- Success Alert --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Error Alert --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <strong>Error!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.absensi.detect-absent') }}" method="POST">
                        @csrf

                        {{-- Mode Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tipe Detection</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="mode" id="mode_single"
                                    value="single" checked onchange="toggleModeFields()">
                                <label class="btn btn-outline-primary" for="mode_single">
                                    <i class="fas fa-calendar-day"></i> Satu Tanggal
                                </label>

                                <input type="radio" class="btn-check" name="mode" id="mode_range"
                                    value="range" onchange="toggleModeFields()">
                                <label class="btn btn-outline-primary" for="mode_range">
                                    <i class="fas fa-calendar-alt"></i> Range Tanggal
                                </label>
                            </div>
                        </div>

                        {{-- Single Date --}}
                        <div id="single_date_field" class="mb-4">
                            <label for="date" class="form-label fw-bold">
                                <i class="fas fa-calendar"></i> Tanggal
                            </label>
                            <input
                                type="date"
                                name="date"
                                id="date"
                                class="form-control"
                                value="{{ old('date', Carbon\Carbon::yesterday()->format('Y-m-d')) }}"
                            >
                            <small class="text-muted">Default: kemarin ({{ Carbon\Carbon::yesterday()->format('Y-m-d') }})</small>
                        </div>

                        {{-- Date Range --}}
                        <div id="range_date_field" class="mb-4" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label fw-bold">Tanggal Mulai</label>
                                    <input
                                        type="date"
                                        name="start_date"
                                        id="start_date"
                                        class="form-control"
                                        value="{{ old('start_date') }}"
                                    >
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label fw-bold">Tanggal Selesai</label>
                                    <input
                                        type="date"
                                        name="end_date"
                                        id="end_date"
                                        class="form-control"
                                        value="{{ old('end_date') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- Info Box --}}
                        <div class="alert alert-info mb-4" role="alert">
                            <i class="fas fa-info-circle"></i>
                            <strong>Cara Kerja:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Sistem akan cek <strong>setiap karyawan</strong> untuk tanggal yang dipilih</li>
                                <li>Jika <strong>tidak ada record absensi</strong> dan <strong>seharusnya kerja</strong> (berdasarkan shift pattern):</li>
                                <li style="margin-left: 20px;"> → Cek dulu apakah ada <strong>cuti disetujui</strong> (sakit, melahirkan, tahunan)</li>
                                <li style="margin-left: 20px;"> → Jika tidak ada cuti → Auto-create record dengan status <strong>"TIDAK HADIR"</strong></li>
                                <li>Karyawan dengan shift pattern "libur" pada hari tersebut akan <strong>skip</strong></li>
                            </ul>
                        </div>

                        {{-- Result Display --}}
                        @if (session('result'))
                            <div class="card mb-4 border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-bar"></i> Hasil Detection
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @php $result = session('result'); @endphp

                                    <div class="row text-center mb-3">
                                        <div class="col-md-6">
                                            <h4 class="text-success">{{ $result['total_created'] ?? 0 }}</h4>
                                            <small class="text-muted">Record Tidak Hadir Created</small>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="text-warning">{{ $result['total_skipped'] ?? 0 }}</h4>
                                            <small class="text-muted">Skipped (Cuti/Libur)</small>
                                        </div>
                                    </div>

                                    @if (!empty($result['created_details']))
                                        <hr>
                                        <h6 class="fw-bold mb-2">Tidak Hadir Detected:</h6>
                                        <div style="max-height: 200px; overflow-y: auto;">
                                            <ul class="list-unstyled">
                                                @foreach ($result['created_details'] as $item)
                                                    <li class="py-1 border-bottom">
                                                        <i class="fas fa-user text-danger"></i>
                                                        <strong>{{ $item['nama'] }}</strong>
                                                        <span class="text-muted">(ID: {{ $item['karyawan_id'] }})</span>
                                                        <span class="badge bg-danger">{{ $item['tanggal'] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Buttons --}}
                        <div class="d-flex gap-2">
                            <button
                                type="submit"
                                class="btn btn-warning btn-lg flex-grow-1"
                            >
                                <i class="fas fa-search"></i> Detect Absent
                            </button>
                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="btn btn-secondary btn-lg"
                            >
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tips --}}
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Automatic:</strong> Sistem akan otomatis detect setiap malam jam 23:59</li>
                        <li><strong>Manual:</strong> Gunakan form ini jika perlu detect untuk tanggal tertentu</li>
                        <li><strong>Backfill:</strong> Gunakan "Range Tanggal" untuk detect berbulan-bulan sekaligus</li>
                        <li><strong>Shift Pattern:</strong> Pastikan shift pattern karyawan sudah diatur (kerja/libur)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleModeFields() {
    const mode = document.querySelector('input[name="mode"]:checked').value;
    const singleField = document.getElementById('single_date_field');
    const rangeField = document.getElementById('range_date_field');

    if (mode === 'single') {
        singleField.style.display = 'block';
        rangeField.style.display = 'none';
    } else {
        singleField.style.display = 'none';
        rangeField.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', toggleModeFields);
</script>

@php
use Carbon\Carbon;
@endphp
@endsection
