@component('mail::message')
# Pengajuan Cuti Ditolak

Pengajuan cuti Anda telah **ditolak** oleh **{{ ucfirst($rejectedBy) }}**.

@if($catatan)
**Alasan penolakan:**  
{{ $catatan }}
@endif

Rincian pengajuan:
- **Jenis Cuti:** {{ $cuti->jenisCuti->nama }}
- **Tanggal:** {{ $cuti->tanggal_mulai->format('d M Y') }} – {{ $cuti->tanggal_selesai->format('d M Y') }}
- **Durasi:** {{ $cuti->jumlah_hari }} hari

Silakan hubungi HRD untuk informasi lebih lanjut.

Terima kasih,  
{{ config('app.name') }}
@endcomponent