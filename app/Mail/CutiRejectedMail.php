<?php

namespace App\Mail;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CutiRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cuti;
    public $catatan;
    public $rejectedBy;

    public function __construct(Cuti $cuti, $catatan, $rejectedBy)
    {
        $this->cuti = $cuti;
        $this->catatan = $catatan;
        $this->rejectedBy = $rejectedBy;
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject('Pengajuan Cuti Ditolak')
                    ->markdown('emails.cuti_rejected')
                    ->with([
                        'cuti' => $this->cuti,
                        'catatan' => $this->catatan,
                        'rejectedBy' => $this->rejectedBy,
                    ]);
    }
}