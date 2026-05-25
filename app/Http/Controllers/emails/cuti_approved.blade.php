@component('mail::message')
# Update Persetujuan Cuti

@if($nextStep)
Pengajuan cuti atas nama **{{ $cuti->karyawan->user->nama }}** telah disetujui oleh **{{ ucfirst($step) }}**.

Selanjutnya menunggu persetujuan dari **{{ $nextApproverName ?? ucfirst($nextStep) }}**.
@else
Pengajuan cuti atas nama **{{ $cuti->karyawan->user->nama }}** telah **disetujui sepenuhnya**.
@endif

@component('mail::button', ['url' => route('admin.approval')])
Lihat Detail
@endcomponent

Terima kasih,  
{{ config('app.name') }}
@endcomponent