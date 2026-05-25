@extends('admin.layouts.app')

@section('title', 'Override Shift Mingguan')

@push('styles')
<style>
    .day-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s;
        background: white;
    }
    .day-card.override-kerja { border-color: #198754; background: #f0fdf4; }
    .day-card.override-libur { border-color: #dc3545; background: #fff5f5; }
    .day-card.from-default   { border-color: #dee2e6; background: #fafafa; opacity: 0.85; }

    .day-label {
        font-weight: 700; font-size: 14px; color: #2d3748;
        margin-bottom: 8px; text-align: center;
    }

    .tipe-toggle {
        display: flex; border-radius: 8px; overflow: hidden;
        border: 1px solid #dee2e6;
    }
    .tipe-toggle input[type="radio"] { display: none; }
    .tipe-toggle label {
        flex: 1; text-align: center; padding: 6px 4px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        transition: all 0.2s; margin: 0; background: #f8f9fa; color: #6c757d;
    }
    .tipe-toggle input[value="kerja"]:checked + label { background: #198754; color: white; }
    .tipe-toggle input[value="libur"]:checked + label { background: #dc3545; color: white; }

    .default-badge {
        display: inline-block; font-size: 10px; font-weight: 600;
        padding: 2px 8px; border-radius: 8px; margin-top: 6px;
        text-align: center; width: 100%;
    }
    .default-badge.kerja { background: #d1fae5; color: #065f46; }
    .default-badge.libur { background: #fee2e2; color: #991b1b; }

    .week-navigator {
        display: flex; align-items: center; gap: 10px;
    }
    .week-btn {
        border: 2px solid #e9ecef; background: white; border-radius: 8px;
        width: 36px; height: 36px; display: flex; align-items: center; justify-content:center;
        cursor: pointer; transition: all 0.2s; color: #6c757d; font-size: 14px;
        text-decoration: none;
    }
    .week-btn:hover { border-color: #0d6efd; color: #0d6efd; background: #eff6ff; }

    .week-display {
        font-weight: 700; font-size: 15px; color: #1e293b;
        text-align: center;
    }
    .week-display small { display: block; font-weight: 400; font-size: 11px; color: #718096; }

    .employee-info { display: flex; align-items: center; gap: 12px; }
    .avatar-initial {
        width: 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 18px; color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        flex-shrink: 0;
    }
    .profile-img {
        width: 44px; height: 44px; border-radius: 50%;
        object-fit: cover; border: 2px solid #e9ecef; flex-shrink: 0;
    }

    .btn-action { padding: 8px 24px; font-weight: 600; font-size: 14px; transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.shift-pattern.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1">Override Shift Mingguan</h4>
        <small class="text-muted">Ubah jadwal untuk minggu tertentu tanpa mengubah default</small>
    </div>
</div>

{{-- Karyawan & Week Navigator --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="employee-info">
                @if(!empty($karyawan->foto_profil))
                    <img src="{{ asset('storage/' . $karyawan->foto_profil) }}"
                         alt="{{ $karyawan->user->nama ?? '' }}" class="profile-img">
                @else
                    <div class="avatar-initial">
                        {{ strtoupper(substr($karyawan->user->nama ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="fw-bold" style="color:#2d3748;font-size:15px;">
                        {{ $karyawan->user->nama ?? '-' }}
                    </div>
                    <div class="text-muted" style="font-size:12px;">NIP: {{ $karyawan->nip ?? '-' }}</div>
                </div>
            </div>

            {{-- Week Navigator --}}
            <div class="week-navigator">
                @php
                    $prevMinggu = $mingguKe - 1;
                    $prevTahun  = $tahun;
                    if ($prevMinggu < 1)  { $prevMinggu = 52; $prevTahun--; }
                    $nextMinggu = $mingguKe + 1;
                    $nextTahun  = $tahun;
                    if ($nextMinggu > 52) { $nextMinggu = 1;  $nextTahun++; }
                @endphp

                <a href="{{ route('admin.shift-pattern.weekly.edit', $karyawan->id) }}?minggu_ke={{ $prevMinggu }}&tahun={{ $prevTahun }}"
                   class="week-btn" title="Minggu sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </a>

                <div class="week-display">
                    Minggu ke-{{ $mingguKe }}, {{ $tahun }}
                    <small>
                        {{ $weekRange['start']->format('d M') }} &ndash; {{ $weekRange['end']->format('d M Y') }}
                    </small>
                </div>

                <a href="{{ route('admin.shift-pattern.weekly.edit', $karyawan->id) }}?minggu_ke={{ $nextMinggu }}&tahun={{ $nextTahun }}"
                   class="week-btn" title="Minggu berikutnya">
                    <i class="fas fa-chevron-right"></i>
                </a>

                {{-- Jump to week --}}
                <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal"
                        data-bs-target="#jumpWeekModal">
                    <i class="fas fa-search me-1"></i> Cari Minggu
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Override status info --}}
@if($weeklyPattern->isNotEmpty())
    <div class="alert border-0 mb-3 d-flex align-items-center gap-2"
         style="background:#fffbeb;border-left:4px solid #f59e0b !important;border-left-style:solid;">
        <i class="fas fa-calendar-check" style="color:#d97706;"></i>
        <div>
            <strong style="font-size:13px;">Override aktif untuk minggu ini.</strong>
            <span style="font-size:13px;color:#6c757d;margin-left:6px;">
                Jadwal di bawah menggantikan default pattern.
            </span>
        </div>
        <form action="{{ route('admin.shift-pattern.weekly.delete', $karyawan->id) }}"
              method="POST" class="ms-auto" id="deleteOverrideForm">
            @csrf @method('DELETE')
            <input type="hidden" name="minggu_ke" value="{{ $mingguKe }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="confirmDelete()">
                <i class="fas fa-trash me-1"></i>Hapus Override
            </button>
        </form>
    </div>
@else
    <div class="alert border-0 mb-3 d-flex align-items-center gap-2"
         style="background:#eff6ff;border-left:4px solid #3b82f6 !important;border-left-style:solid;">
        <i class="fas fa-info-circle" style="color:#1d4ed8;"></i>
        <span style="font-size:13px;">
            Minggu ini menggunakan <strong>default pattern</strong>. Kartu di bawah menampilkan default sebagai referensi.
        </span>
    </div>
@endif

{{-- Form --}}
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-0 pt-4 px-4" style="background:white;">
        <h6 class="fw-bold mb-0">
            <i class="fas fa-calendar-week me-2 text-warning"></i>
            Jadwal Minggu ke-{{ $mingguKe }}/{{ $tahun }}
        </h6>
        <small class="text-muted">
            Badge abu-abu = default. Ubah toggle untuk membuat override minggu ini.
        </small>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.shift-pattern.weekly.update', $karyawan->id) }}" method="POST">
            @csrf
            <input type="hidden" name="minggu_ke" value="{{ $mingguKe }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">

            @php
                $hariList = [
                    'senin'  => 'Senin',  'selasa' => 'Selasa', 'rabu'   => 'Rabu',
                    'kamis'  => 'Kamis',  'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu', 'minggu' => 'Minggu',
                ];

                // Default pattern sebagai map hari => data
                $defaultMap = [];
                if ($defaultPattern) {
                    foreach ($defaultPattern as $dp) {
                        $defaultMap[$dp->hari] = [
                            'tipe' => $dp->tipe,
                            'shift_id' => $dp->shift_id
                        ];
                    }
                }
            @endphp

            <div class="row g-3 mb-4">
                @foreach($hariList as $hari => $label)
                    @php
                        $weeklyP  = $weeklyPattern->get($hari);
                        $defaultP = $defaultMap[$hari] ?? null;
                        
                        $currentTipe = $weeklyP ? $weeklyP->tipe : ($defaultP['tipe'] ?? 'kerja');
                        $currentShiftId = $weeklyP ? $weeklyP->shift_id : ($defaultP['shift_id'] ?? null);
                        $isOverride  = (bool)$weeklyP;
                    @endphp
                    <div class="col-6 col-sm-4 col-md-3 col-lg" style="min-width:140px;">
                        <div class="day-card {{ $isOverride ? 'override-'.$currentTipe : 'from-default' }}"
                             id="card-{{ $hari }}">
                            <div class="day-label" onclick="updateCard('{{ $hari }}', document.getElementById('kerja_{{ $hari }}').checked ? 'libur' : 'kerja'); document.getElementById(document.getElementById('kerja_{{ $hari }}').checked ? 'libur_{{ $hari }}' : 'kerja_{{ $hari }}').checked = true;">{{ $label }}</div>

                            <input type="hidden" name="hari[]" value="{{ $hari }}">

                            <div class="tipe-toggle mb-2">
                                <input type="radio" name="tipe_{{ $hari }}" id="kerja_{{ $hari }}"
                                       value="kerja"
                                       {{ $currentTipe === 'kerja' ? 'checked' : '' }}
                                       onchange="updateCard('{{ $hari }}', 'kerja')">
                                <label for="kerja_{{ $hari }}">
                                    <i class="fas fa-briefcase"></i> Kerja
                                </label>

                                <input type="radio" name="tipe_{{ $hari }}" id="libur_{{ $hari }}"
                                       value="libur"
                                       {{ $currentTipe === 'libur' ? 'checked' : '' }}
                                       onchange="updateCard('{{ $hari }}', 'libur')">
                                <label for="libur_{{ $hari }}">
                                    <i class="fas fa-umbrella-beach"></i> Libur
                                </label>
                            </div>
                            
                            {{-- Dropdown Shift (Hanya tampil jika tipe=kerja) --}}
                            <select name="shift_id[{{ $hari }}]" id="shift_id_{{ $hari }}" class="form-select form-select-sm mt-2" {{ $currentTipe === 'kerja' ? '' : 'disabled style=display:none;' }}>
                                <option value="">Pilih Shift...</option>
                                @foreach($shifts as $s)
                                    <option value="{{ $s->id }}" {{ $currentShiftId == $s->id ? 'selected' : '' }}>
                                        {{ $s->kode }} ({{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang, 0, 5) }})
                                    </option>
                                @endforeach
                            </select>

                            <input type="hidden" name="tipe[]" id="tipeValue_{{ $hari }}"
                                   value="{{ $currentTipe }}">

                            {{-- Default badge --}}
                            @if($defaultP)
                                <div class="default-badge {{ $defaultP['tipe'] }}">
                                    Default: {{ ucfirst($defaultP['tipe']) }}
                                </div>
                            @else
                                <div class="default-badge" style="background:#f1f3f5;color:#adb5bd;">
                                    Tak ada default
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.shift-pattern.index') }}" class="btn btn-light btn-action">
                    Batal
                </a>
                <button type="submit" class="btn btn-warning btn-action text-dark">
                    <i class="fas fa-save me-2"></i>Simpan Override
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Jump Week Modal --}}
<div class="modal fade" id="jumpWeekModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-search" style="color:#1d4ed8;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Cari Minggu</h6>
                        <small class="text-muted" style="font-size:11px;">Langsung ke minggu tertentu</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form action="{{ route('admin.shift-pattern.weekly.edit', $karyawan->id) }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Minggu ke-</label>
                        <input type="number" name="minggu_ke" class="form-control" style="font-size:13px;"
                               min="1" max="52" value="{{ $mingguKe }}" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">Tahun</label>
                        <input type="number" name="tahun" class="form-control" style="font-size:13px;"
                               min="2020" value="{{ $tahun }}" required>
                    </div>
                    <div class="modal-footer border-0 pt-3 px-0">
                        <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                            <i class="fas fa-arrow-right me-1"></i> Pergi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteOverrideModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0" style="background:#fef2f2;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-trash" style="color:#dc2626;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Hapus Override</h6>
                        <small class="text-muted" style="font-size:11px;">Tindakan tidak bisa dibatalkan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p style="font-size:13px;" class="mb-1">Yakin hapus override minggu ke-</p>
                <div class="fw-bold" style="font-size:15px;color:#1e293b;">
                    {{ $mingguKe }}/{{ $tahun }}
                </div>
                <p class="text-muted mt-2" style="font-size:12px;">
                    Jadwal akan kembali ke default pattern.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm px-4 fw-semibold"
                        onclick="document.getElementById('deleteOverrideForm').submit()">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function updateCard(hari, tipe) {
        const card   = document.getElementById('card-' + hari);
        const hidden = document.getElementById('tipeValue_' + hari);
        const shiftSelect = document.getElementById('shift_id_' + hari);
        
        card.classList.remove('override-kerja', 'override-libur', 'from-default');
        card.classList.add('override-' + tipe);
        hidden.value = tipe;
        
        if (tipe === 'kerja') {
            shiftSelect.disabled = false;
            shiftSelect.style.display = 'block';
        } else {
            shiftSelect.disabled = true;
            shiftSelect.style.display = 'none';
        }
    }

    function confirmDelete() {
        const modal = new bootstrap.Modal(document.getElementById('deleteOverrideModal'));
        modal.show();
    }

    // Sync hidden tipe[] on submit
    document.querySelectorAll('form[action*="weekly"]').forEach(form => {
        if (!form.id) {
            form.addEventListener('submit', function() {
                ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'].forEach(hari => {
                    const kerja  = document.getElementById('kerja_' + hari);
                    const hidden = document.getElementById('tipeValue_' + hari);
                    if (kerja && hidden) hidden.value = kerja.checked ? 'kerja' : 'libur';
                });
            });
        }
    });

    function showToast(message, type = 'success') {
        const colors = { success: '#28a745', error: '#dc3545' };
        const toast  = document.createElement('div');
        toast.style.cssText = `
            position:fixed;top:20px;right:20px;padding:15px 25px;
            background:${colors[type]};color:white;border-radius:8px;
            font-weight:600;font-size:14px;z-index:9999;
            box-shadow:0 4px 12px rgba(0,0,0,0.15);
            animation:slideInRight 0.3s ease-out;
            display:flex;align-items:center;gap:10px;
        `;
        toast.innerHTML = `<i class="fas fa-check-circle"></i><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity='0'; toast.style.transition='opacity 0.3s'; setTimeout(()=>toast.remove(),300); }, 3500);
    }

    @if(session('success')) showToast(`{!! session('success') !!}`, 'success'); @endif
    @if(session('error'))   showToast(`{!! session('error') !!}`, 'error');   @endif
</script>
@endpush
