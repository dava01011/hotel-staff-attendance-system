@extends('admin.layouts.app')

@section('title', 'Edit Hari Libur Nasional')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.hari-libur-nasional.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1">Edit Hari Libur Nasional</h4>
        <small class="text-muted">{{ $hariLibur->nama }}</small>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 pt-4 px-4" style="background:#fffbeb;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;border-radius:10px;background:#fef3c7;
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-edit" style="color:#d97706;font-size:15px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size:14px;">Edit Hari Libur</h6>
                            <small class="text-muted" style="font-size:11px;">{{ $hariLibur->nama }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.hari-libur-nasional.update', $hariLibur->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="card-body pt-4">

                    {{-- Info tanggal (readonly untuk semua tipe) --}}
                    <div class="mb-3 p-3 rounded-3" style="background:#f8fafc;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted" style="font-size:11px;font-weight:600;">TANGGAL</div>
                                <div class="fw-bold" style="font-size:15px;color:#1d4ed8;">
                                    {{ \Carbon\Carbon::parse($hariLibur->tanggal)->format('d M Y') }}
                                </div>
                                <div class="text-muted" style="font-size:12px;">
                                    {{ \Carbon\Carbon::parse($hariLibur->tanggal)->locale('id')->isoFormat('dddd') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Tipe Libur</label>
                        <select name="tipe" id="tipeSelect" class="form-select @error('tipe') is-invalid @enderror" style="font-size:13px;">
                            <option value="fixed" {{ old('tipe', $hariLibur->tipe) === 'fixed' ? 'selected' : '' }}>📌 Tanggal Tetap (Fixed/Recurring)</option>
                            <option value="dynamic" {{ old('tipe', $hariLibur->tipe) === 'dynamic' ? 'selected' : '' }}>🔄 Tanggal Bervariasi (Dynamic)</option>
                            <option value="manual" {{ old('tipe', $hariLibur->tipe) === 'manual' ? 'selected' : '' }}>✏️ Input Manual</option>
                        </select>
                        @error('tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Nama Hari Libur <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama"
                                class="form-control @error('nama') is-invalid @enderror"
                                style="font-size:13px;"
                                value="{{ old('nama', $hariLibur->nama) }}" required>
                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Recurring Checkbox (hanya muncul jika fixed) --}}
                    <div id="recurringSection" class="mb-3 p-3 rounded-3" style="background:#eff6ff; display: {{ old('tipe', $hariLibur->tipe) === 'fixed' ? 'block' : 'none' }};">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-bold" style="font-size:13px; color:#1d4ed8;">Auto-Recurring</div>
                                <div class="text-muted" style="font-size:11px;">Jadikan libur ini muncul setiap tahun pada tanggal yang sama.</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_recurring"
                                        value="1" id="isRecurring"
                                        {{ old('is_recurring', $hariLibur->is_recurring) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    {{-- Keterangan (semua tipe) --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">Keterangan</label>
                        <textarea name="keterangan" class="form-control" style="font-size:13px;" rows="2">{{ old('keterangan', $hariLibur->keterangan) }}</textarea>
                    </div>

                </div>
                <div class="card-footer border-0 d-flex justify-content-end gap-2 pb-4 px-4"
                     style="background:#fffbeb;">
                    <a href="{{ route('admin.hari-libur-nasional.index') }}" class="btn btn-light btn-sm px-4">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-sm px-4 fw-semibold"
                            style="background:#fbbf24;color:#1e293b;border:none;">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeSelect = document.getElementById('tipeSelect');
    const recurringSection = document.getElementById('recurringSection');

    tipeSelect.addEventListener('change', function() {
        if (this.value === 'fixed') {
            recurringSection.style.display = 'block';
        } else {
            recurringSection.style.display = 'none';
        }
    });
});
</script>
@endpush
