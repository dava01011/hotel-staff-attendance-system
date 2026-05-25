{{-- File: resources/views/karyawan/shift_pattern.blade.php --}}
{{-- View untuk karyawan lihat shift pattern mereka --}}

@extends('karyawan.layout.fullscreen')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> Jadwal Shift Saya
                    </h5>
                </div>

                <div class="card-body">
                    {{-- Info --}}
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Penjelasan:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Default Pattern:</strong> Jadwal tetap Anda yang berlaku setiap minggu</li>
                            <li><strong>Weekly Override:</strong> Jika ada perubahan/tukar shift untuk minggu tertentu</li>
                            <li>Jika ada weekly override, gunakan itu. Jika tidak ada, pakai default pattern</li>
                        </ul>
                    </div>

                    {{-- DEFAULT PATTERN --}}
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-star"></i> Default Pattern (Jadwal Tetap)
                    </h6>

                    <div class="table-responsive mb-5">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Hari</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $hariOrder = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
                                @endphp
                                @foreach ($hariOrder as $hari)
                                    @php
                                        $pattern = $defaultPattern[$hari] ?? null;
                                        $tipe = $pattern?->tipe;
                                        $badge = $tipe === 'libur' ? 'badge-danger' : 'badge-success';
                                        $label = $tipe === 'libur' ? 'Libur' : 'Kerja';
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">
                                            {{ \App\Models\KaryawanShiftPattern::getLabelHari($hari) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badge }}">{{ $label }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- CURRENT WEEK PATTERN --}}
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-calendar-week"></i> Minggu Ini
                        (Minggu {{ $currentWeek['minggu_ke'] }}/{{ $currentWeek['tahun'] }} - {{ $currentWeek['start']->format('d M') }} s/d {{ $currentWeek['end']->format('d M Y') }})
                    </h6>

                    @if ($currentWeekOverride && $currentWeekOverride->count() > 0)
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Ada perubahan/tukar shift minggu ini!</strong>
                        </div>

                        <div class="table-responsive mb-5">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hari</th>
                                        <th class="text-center">Status (Minggu Ini)</th>
                                        <th class="text-center">Default</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hariOrder as $hari)
                                        @php
                                            $weeklyPattern = $currentWeekOverride[$hari] ?? null;
                                            $defaultPattern = $defaultPattern[$hari] ?? null;

                                            $weeklyTipe = $weeklyPattern?->tipe;
                                            $defaultTipe = $defaultPattern?->tipe;

                                            $weeklyBadge = $weeklyTipe === 'libur' ? 'badge-danger' : 'badge-success';
                                            $weeklyLabel = $weeklyTipe === 'libur' ? 'Libur' : 'Kerja';

                                            $defaultBadge = $defaultTipe === 'libur' ? 'badge-outline-danger' : 'badge-outline-success';
                                            $defaultLabel = $defaultTipe === 'libur' ? 'Libur' : 'Kerja';

                                            $isChanged = $weeklyTipe !== $defaultTipe;
                                        @endphp
                                        <tr @if($isChanged) class="bg-warning bg-opacity-10" @endif>
                                            <td class="fw-bold">
                                                {{ \App\Models\KaryawanShiftPattern::getLabelHari($hari) }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $weeklyBadge }}">{{ $weeklyLabel }}</span>
                                                @if($isChanged)
                                                    <br><small class="text-muted">(Berubah dari default)</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $defaultBadge }}">{{ $defaultLabel }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success mb-5">
                            <i class="fas fa-check-circle"></i>
                            Minggu ini menggunakan <strong>Default Pattern</strong> (tidak ada perubahan)
                        </div>
                    @endif

                    {{-- NEXT 4 WEEKS OVERVIEW --}}
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-chart-calendar"></i> 4 Minggu Ke Depan
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Minggu</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($nextWeeks as $week)
                                    @php
                                        $hasOverride = \App\Models\KaryawanShiftPattern::hasWeeklyOverride(
                                            auth()->user()->karyawan->id,
                                            $week['minggu_ke'],
                                            $week['tahun']
                                        );
                                    @endphp
                                    <tr @if($hasOverride) class="fw-bold text-warning" @endif>
                                        <td>Minggu {{ $week['minggu_ke'] }}/{{ $week['tahun'] }}</td>
                                        <td>{{ $week['start']->format('d M Y') }} - {{ $week['end']->format('d M Y') }}</td>
                                        <td>
                                            @if($hasOverride)
                                                <span class="badge bg-warning">Ada Perubahan</span>
                                            @else
                                                <span class="badge bg-secondary">Default Pattern</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- NOTES --}}
                    <div class="mt-4 p-3 bg-light border-left-4 border-primary">
                        <h6 class="fw-bold mb-2">📝 Catatan Penting:</h6>
                        <ul class="mb-0">
                            <li>Jadwal shift Anda ditentukan oleh admin/manager</li>
                            <li>Jika ada perubahan (tukar shift), akan ditampilkan di atas</li>
                            <li>Minggu berikutnya, jika tidak ada override, kembali ke default pattern</li>
                            <li>Untuk request tukar shift, hubungi manager atau melalui fitur ajukan shift</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-4 {
        border-left: 4px solid !important;
    }

    .badge-outline-success {
        background-color: #e7f3ff;
        color: #28a745;
        border: 1px solid #28a745;
    }

    .badge-outline-danger {
        background-color: #ffe7e7;
        color: #dc3545;
        border: 1px solid #dc3545;
    }
</style>
@endsection
