@extends('admin.layouts.app')

@section('title', 'Edit Default Pattern')

@push('styles')
<style>
    .day-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s;
        cursor: pointer;
        background: white;
    }
    .day-card:hover { border-color: #0d6efd; box-shadow: 0 2px 8px rgba(13,110,253,0.1); }
    .day-card.selected-kerja { border-color: #198754; background: #f0fdf4; }
    .day-card.selected-libur { border-color: #dc3545; background: #fff5f5; }

    .day-label {
        font-weight: 700;
        font-size: 15px;
        color: #2d3748;
        margin-bottom: 10px;
        text-align: center;
    }
    .day-label small { display: block; font-weight: 400; font-size: 11px; color: #718096; }

    .tipe-toggle {
        display: flex;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dee2e6;
    }
    .tipe-toggle label {
        flex: 1;
        text-align: center;
        padding: 6px 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin: 0;
    }
    .tipe-toggle input[type="radio"] { display: none; }
    .tipe-toggle input[type="radio"]:checked + label.kerja-label {
        background: #198754; color: white;
    }
    .tipe-toggle input[type="radio"]:checked + label.libur-label {
        background: #dc3545; color: white;
    }
    .tipe-toggle label.kerja-label:not(:has(~ input:checked)) { background: #f8f9fa; color: #6c757d; }
    .tipe-toggle label.libur-label { background: #f8f9fa; color: #6c757d; }

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

    .pattern-summary {
        display: flex; flex-wrap: wrap; gap: 6px; align-items: center;
    }
    .pattern-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
        transition: all 0.2s;
    }
    .pattern-chip.kerja { background: #d1fae5; color: #065f46; }
    .pattern-chip.libur { background: #fee2e2; color: #991b1b; }
    .pattern-chip.unset { background: #f1f3f5; color: #adb5bd; }

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
        <h4 class="fw-bold mb-1">Edit Default Pattern</h4>
        <small class="text-muted">Atur jadwal kerja default per hari dalam seminggu</small>
    </div>
</div>

{{-- Karyawan Info Card --}}
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

            {{-- Live Summary --}}
            <div>
                <div style="font-size:12px;color:#718096;margin-bottom:6px;">Preview Pattern:</div>
                <div class="pattern-summary" id="patternSummary">
                    <span class="pattern-chip unset">Belum ada pilihan</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form --}}
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-0 pt-4 px-4" style="background:white;">
        <h6 class="fw-bold mb-0">
            <i class="fas fa-sliders-h me-2 text-primary"></i>Pengaturan Hari Kerja
        </h6>
        <small class="text-muted">Klik kartu hari untuk memilih, lalu tentukan tipe Kerja atau Libur</small>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.shift-pattern.default.update', $karyawan->id) }}" method="POST"
              id="defaultPatternForm">
            @csrf

            @php
                $hariList = [
                    'senin'   => ['label' => 'Senin',   'short' => 'Sen'],
                    'selasa'  => ['label' => 'Selasa',  'short' => 'Sel'],
                    'rabu'    => ['label' => 'Rabu',    'short' => 'Rab'],
                    'kamis'   => ['label' => 'Kamis',   'short' => 'Kam'],
                    'jumat'   => ['label' => 'Jumat',   'short' => 'Jum'],
                    'sabtu'   => ['label' => 'Sabtu',   'short' => 'Sab'],
                    'minggu'  => ['label' => 'Minggu',  'short' => 'Min'],
                ];
                // $pattern bisa berupa Collection atau array
                $patternData = [];
                if ($pattern) {
                    foreach ($pattern as $p) {
                        $patternData[$p->hari] = [
                            'tipe' => $p->tipe,
                            'shift_id' => $p->shift_id
                        ];
                    }
                }
            @endphp

            <div class="row g-3 mb-4">
                @foreach($hariList as $hari => $info)
                    @php
                        $currentTipe = $patternData[$hari]['tipe'] ?? null;
                        $currentShiftId = $patternData[$hari]['shift_id'] ?? null;
                    @endphp
                    <div class="col-6 col-sm-4 col-md-3 col-lg" style="min-width:140px;">
                        <div class="day-card {{ $currentTipe ? 'selected-'.$currentTipe : '' }}"
                             id="card-{{ $hari }}">

                            <div class="day-label" onclick="selectCard('{{ $hari }}')">
                                {{ $info['label'] }}
                            </div>

                            {{-- Hidden hari --}}
                            <input type="hidden" name="hari[]" value="{{ $hari }}">

                            {{-- Tipe Toggle --}}
                            <div class="tipe-toggle mb-2">
                                <input type="radio" name="tipe_{{ $hari }}" id="kerja_{{ $hari }}"
                                       value="kerja"
                                       {{ $currentTipe === 'kerja' ? 'checked' : '' }}
                                       onchange="updateCard('{{ $hari }}', 'kerja')">
                                <label for="kerja_{{ $hari }}" class="kerja-label">
                                    <i class="fas fa-briefcase"></i> Kerja
                                </label>

                                <input type="radio" name="tipe_{{ $hari }}" id="libur_{{ $hari }}"
                                       value="libur"
                                       {{ $currentTipe === 'libur' ? 'checked' : '' }}
                                       onchange="updateCard('{{ $hari }}', 'libur')">
                                <label for="libur_{{ $hari }}" class="libur-label">
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

                            {{-- Actual tipe input untuk form submit --}}
                            <input type="hidden" name="tipe[]" id="tipeValue_{{ $hari }}"
                                   value="{{ $currentTipe ?? 'kerja' }}">
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Info box --}}
            <div class="alert alert-info border-0" style="background:#eff6ff;font-size:13px;">
                <i class="fas fa-info-circle me-2" style="color:#1d4ed8;"></i>
                Default pattern ini berlaku setiap minggu, kecuali ada <strong>override mingguan</strong>.
            </div>

            {{-- Buttons --}}
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.shift-pattern.index') }}"
                   class="btn btn-light btn-action">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary btn-action">
                    <i class="fas fa-save me-2"></i>Simpan Pattern
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const hariList   = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
    const labelShort = { senin:'Sen', selasa:'Sel', rabu:'Rab', kamis:'Kam', jumat:'Jum', sabtu:'Sab', minggu:'Min' };

    function selectCard(hari) {
        // Toggle antara kerja & libur saat klik card
        const kerjaRadio = document.getElementById('kerja_' + hari);
        const liburRadio = document.getElementById('libur_' + hari);
        if (!kerjaRadio.checked && !liburRadio.checked) {
            kerjaRadio.checked = true;
            updateCard(hari, 'kerja');
        }
    }

    function updateCard(hari, tipe) {
        const card = document.getElementById('card-' + hari);
        const hidden = document.getElementById('tipeValue_' + hari);
        const shiftSelect = document.getElementById('shift_id_' + hari);

        card.classList.remove('selected-kerja', 'selected-libur');
        card.classList.add('selected-' + tipe);
        hidden.value = tipe;

        if (tipe === 'kerja') {
            shiftSelect.disabled = false;
            shiftSelect.style.display = 'block';
        } else {
            shiftSelect.disabled = true;
            shiftSelect.style.display = 'none';
        }

        updateSummary();
    }

    function updateSummary() {
        const summary = document.getElementById('patternSummary');
        let chips = '';

        hariList.forEach(hari => {
            const kerjaRadio = document.getElementById('kerja_' + hari);
            const liburRadio = document.getElementById('libur_' + hari);
            if (kerjaRadio && kerjaRadio.checked) {
                chips += `<span class="pattern-chip kerja"><i class="fas fa-briefcase"></i>${labelShort[hari]}</span>`;
            } else if (liburRadio && liburRadio.checked) {
                chips += `<span class="pattern-chip libur"><i class="fas fa-umbrella-beach"></i>${labelShort[hari]}</span>`;
            }
        });

        summary.innerHTML = chips || '<span class="pattern-chip unset">Belum ada pilihan</span>';
    }

    // Sync hidden inputs saat form submit (ensure tipe[] parallel dengan hari[])
    document.getElementById('defaultPatternForm').addEventListener('submit', function() {
        hariList.forEach(hari => {
            const kerjaRadio = document.getElementById('kerja_' + hari);
            const hidden = document.getElementById('tipeValue_' + hari);
            hidden.value = kerjaRadio.checked ? 'kerja' : 'libur';
        });
    });

    // Init summary
    updateSummary();

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
        setTimeout(() => { toast.style.opacity='0'; toast.style.transition='opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 3500);
    }

    @if(session('success')) showToast(`{!! session('success') !!}`, 'success'); @endif
    @if(session('error'))   showToast(`{!! session('error') !!}`, 'error');   @endif
</script>
@endpush
