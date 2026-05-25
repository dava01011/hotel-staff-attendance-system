@component('mail::message')
# Pengajuan Cuti Baru

Yth. Bapak/Ibu,

{{ $cuti->karyawan->user->nama }} (NIP: {{ $cuti->karyawan->nip }}) telah mengajukan cuti dengan rincian:

- **Jenis Cuti:** {{ $cuti->jenisCuti->nama }}
- **Tanggal:** {{ $cuti->tanggal_mulai->format('d M Y') }} – {{ $cuti->tanggal_selesai->format('d M Y') }}
- **Durasi:** {{ $cuti->jumlah_hari }} hari
- **Alasan:** {{ $cuti->alasan }}

@component('mail::button', ['url' => route('admin.approval')])
Lihat & Proses
@endcomponent

Terima kasih,  
{{ config('app.name') }}
@endcomponent