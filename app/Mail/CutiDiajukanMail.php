<?php

namespace App\Mail;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CutiDiajukanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cuti;

    public function __construct(Cuti $cuti)
    {
        $this->cuti = $cuti;
    }

    public function build()
    {
        $subject = '[PENGAJUAN BARU] Cuti - ' . $this->cuti->karyawan->user->nama;

        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject($subject)
                    ->markdown('emails.cuti_diajukan')
                    ->with(['cuti' => $this->cuti]);
    }
}