@extends('admin.layouts.app')

@section('title', 'Pengajuan Shift Departemen')

@section('content')

<style>
    .shift-container {
        padding: 24px;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-icon.blue {
        background: #e3f2fd;
        color: #1976d2;
    }

    .stat-icon.orange {
        background: #fff3e0;
        color: #f57c00;
    }

    .stat-icon.green {
        background: #e8f5e9;
        color: #388e3c;
    }

    .stat-icon.red {
        background: #ffebee;
        color: #d32f2f;
    }

    .stat-info h3 {
        font-size: 28px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 4px;
    }

    .stat-info p {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
    }

    /* Current Shift Card */
    .current-shift-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .current-shift-card h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #212529;
    }

    .shift-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .shift-detail-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .shift-detail-icon {
        width: 48px;
        height: 48px;
        /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
        background: #354591;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .shift-detail-text h4 {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .shift-detail-text p {
        font-size: 15px;
        font-weight: 600;
        color: #212529;
        margin: 0;
    }

    /* Action Button */
    .action-button {
        /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
        background: #354591;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
    }

    .action-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Table */
    .table-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .table-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #212529;
        margin: 0;
    }

    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f8f9fa;
    }

    th {
        padding: 14px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 16px 14px;
        border-bottom: 1px solid #e9ecef;
        color: #212529;
        font-size: 14px;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-disetujui {
        background: #d4edda;
        color: #155724;
    }

    .badge-ditolak {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-sementara {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-permanen {
        background: #e2e3e5;
        color: #383d41;
    }

    /* Modal */
    .modal-body .form-group {
        margin-bottom: 20px;
    }

    .modal-body label {
        display: block;
        font-weight: 600;
        color: #212529;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .modal-body .form-control,
    .modal-body .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        outline: none;
        border-color: #354591;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .radio-group {
        display: flex;
        gap: 16px;
        margin-top: 8px;
    }

    .radio-option {
        flex: 1;
    }

    .radio-option input[type="radio"] {
        display: none;
    }

    .radio-label {
        display: block;
        padding: 12px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }

    .radio-option input[type="radio"]:checked + .radio-label {
        border-color: #354591;
        background: #f0f4ff;
        color: #354591;
    }

    .date-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Alert */
    .alert-info {
        background: #e3f2fd;
        border-left: 4px solid #1976d2;
        padding: 14px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-info i {
        color: #1976d2;
        font-size: 20px;
    }

    .alert-warning {
        background: #fff3cd;
        border-left: 4px solid #f57c00;
        padding: 14px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 36px;
    }

    .empty-text {
        font-size: 16px;
        color: #495057;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .empty-subtext {
        font-size: 14px;
        color: #adb5bd;
    }
</style>

<div class="shift-container">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalPengajuan }}</h3>
                <p>Total Pengajuan</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $pengajuanPending }}</h3>
                <p>Menunggu Approval</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $pengajuanDisetujui }}</h3>
                <p>Disetujui</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $pengajuanDitolak }}</h3>
                <p>Ditolak</p>
            </div>
        </div>
    </div>

    <!-- Current Shift -->
    @if($jadwalShiftAktif)
    <div class="current-shift-card">
        <h3>Shift Aktif Departemen {{ $departemen->nama }}</h3>

        <div class="shift-details">
            <div class="shift-detail-item">
                <div class="shift-detail-icon">
                    <i class="fas fa-id-badge"></i>
                </div>
                <div class="shift-detail-text">
                    <h4>Kode Shift</h4>
                    <p>{{ $jadwalShiftAktif->shift->kode }}</p>
                </div>
            </div>

            <div class="shift-detail-item">
                <div class="shift-detail-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="shift-detail-text">
                    <h4>Jenis Shift</h4>
                    <p>{{ $jadwalShiftAktif->shift->jenis }}</p>
                </div>
            </div>

            <div class="shift-detail-item">
                <div class="shift-detail-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="shift-detail-text">
                    <h4>Jam Masuk</h4>
                    <p>{{ date('H:i', strtotime($jadwalShiftAktif->shift->jam_masuk)) }}</p>
                </div>
            </div>

            <div class="shift-detail-item">
                <div class="shift-detail-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="shift-detail-text">
                    <h4>Jam Pulang</h4>
                    <p>{{ date('H:i', strtotime($jadwalShiftAktif->shift->jam_pulang)) }}</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px;">
            @if($pendingPengajuan)
                <div class="alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Anda memiliki pengajuan shift yang sedang menunggu persetujuan. Tidak dapat mengajukan shift baru sampai pengajuan sebelumnya diproses.
                </div>
                <button class="action-button" disabled>
                    <i class="fas fa-plus"></i> Ajukan Pergantian Shift
                </button>
            @else
                <button class="action-button" data-bs-toggle="modal" data-bs-target="#ajukanShiftModal">
                    <i class="fas fa-plus"></i> Ajukan Pergantian Shift
                </button>
            @endif
        </div>
    </div>
    @endif

    <!-- Riwayat Pengajuan -->
    <div class="table-card">
        <div class="table-header">
            <h3>Riwayat Pengajuan Shift</h3>
        </div>

        @if($riwayatPengajuan->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <div class="empty-text">Belum ada riwayat pengajuan</div>
                <div class="empty-subtext">Semua pengajuan shift akan muncul di sini</div>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Shift Lama</th>
                            <th>Shift Baru</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Diajukan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatPengajuan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge badge-{{ $item->jenis }}">
                                    {{ ucfirst($item->jenis) }}
                                </span>
                            </td>
                            <td>{{ $item->shiftLama->jenis ?? '-' }}</td>
                            <td><strong>{{ $item->shiftBaru->jenis ?? '-' }}</strong></td>
                            <td>
                                {{ date('d/m/Y', strtotime($item->tanggal_mulai)) }}
                                @if($item->tanggal_selesai)
                                    - {{ date('d/m/Y', strtotime($item->tanggal_selesai)) }}
                                @else
                                    - Seterusnya
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $item->status }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($item->status == 'pending')
                                    <button class="btn btn-sm btn-danger" onclick="cancelPengajuan({{ $item->id }})">
                                        <i class="fas fa-times"></i> Batalkan
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px;">
                {{ $riwayatPengajuan->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Ajukan Shift -->
<div class="modal fade" id="ajukanShiftModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clock"></i> Ajukan Pergantian Shift
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin-dept.shift.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span>Pastikan Anda mengisi form dengan benar. Pengajuan akan direview oleh Super Admin.</span>
                    </div>

                    <div class="form-group">
                        <label>Jenis Pengajuan <span class="text-danger">*</span></label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" name="jenis" value="sementara" id="jenis_sementara" required onchange="toggleTanggalSelesai()">
                                <label for="jenis_sementara" class="radio-label">
                                    <i class="fas fa-calendar-alt"></i><br>
                                    Sementara
                                </label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" name="jenis" value="permanen" id="jenis_permanen" required onchange="toggleTanggalSelesai()">
                                <label for="jenis_permanen" class="radio-label">
                                    <i class="fas fa-infinity"></i><br>
                                    Permanen
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Shift Pengganti <span class="text-danger">*</span></label>
                        <select name="shift_baru_id" class="form-select" required>
                            <option value="">-- Pilih Shift Pengganti --</option>
                            @foreach($allShifts as $shift)
                                @if($jadwalShiftAktif && $shift->id != $jadwalShiftAktif->shift_id)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->kode }} - {{ $shift->jenis }} ({{ date('H:i', strtotime($shift->jam_masuk)) }} - {{ date('H:i', strtotime($shift->jam_pulang)) }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="date-row">
                        <div class="form-group">
                            <label>Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group" id="tanggal_selesai_group" style="display: none;">
                            <label>Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_selesai" class="form-control" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alasan (Opsional)</label>
                        <textarea name="alasan" class="form-control" rows="4" placeholder="Jelaskan alasan pengajuan pergantian shift..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail untuk setiap pengajuan -->
@foreach($riwayatPengajuan as $item)
<div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengajuan Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tr>
                        <th width="40%">Jenis</th>
                        <td><span class="badge badge-{{ $item->jenis }}">{{ ucfirst($item->jenis) }}</span></td>
                    </tr>
                    <tr>
                        <th>Shift Lama</th>
                        <td>{{ $item->shiftLama->jenis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Shift Baru</th>
                        <td>{{ $item->shiftBaru->jenis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>
                            {{ date('d/m/Y', strtotime($item->tanggal_mulai)) }}
                            @if($item->tanggal_selesai)
                                - {{ date('d/m/Y', strtotime($item->tanggal_selesai)) }}
                            @else
                                - Seterusnya
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge badge-{{ $item->status }}">{{ ucfirst($item->status) }}</span></td>
                    </tr>
                    <tr>
                        <th>Alasan</th>
                        <td>{{ $item->alasan ?? '-' }}</td>
                    </tr>
                    @if($item->catatan_admin)
                    <tr>
                        <th>Catatan Admin</th>
                        <td>{{ $item->catatan_admin }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Diajukan Oleh</th>
                        <td>{{ $item->pemohon->nama ?? '-' }}</td>
                    </tr>
                    @if($item->approver)
                    <tr>
                        <th>Disetujui Oleh</th>
                        <td>{{ $item->approver->nama ?? '-' }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    function toggleTanggalSelesai() {
        const sementara = document.getElementById('jenis_sementara').checked;
        const tanggalSelesaiGroup = document.getElementById('tanggal_selesai_group');
        const tanggalSelesaiInput = document.querySelector('input[name="tanggal_selesai"]');

        if (sementara) {
            tanggalSelesaiGroup.style.display = 'block';
            tanggalSelesaiInput.required = true;
        } else {
            tanggalSelesaiGroup.style.display = 'none';
            tanggalSelesaiInput.required = false;
        }
    }

    function cancelPengajuan(id) {
        if (confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')) {
            window.location.href = `/admin-dept/shift/cancel/${id}`;
        }
    }
</script>

@endsection
