<?php

namespace App\Mail;

use App\Models\Cuti;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CutiApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cuti;
    public $step;           // step yang baru disetujui (manager/gm/hrd)
    public $nextStep;       // step berikutnya (jika ada)
    public $nextApproverName;

    public function __construct(Cuti $cuti, $step, $nextStep = null, $nextApproverName = null)
    {
        $this->cuti = $cuti;
        $this->step = $step;
        $this->nextStep = $nextStep;
        $this->nextApproverName = $nextApproverName;
    }

    public function build()
    {
        if ($this->nextStep) {
            $subject = "Cuti Disetujui oleh {$this->step} - Menunggu {$this->nextStep}";
        } else {
            $subject = "Cuti Disetujui Sepenuhnya";
        }

        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject($subject)
                    ->markdown('emails.cuti_approved')
                    ->with([
                        'cuti' => $this->cuti,
                        'step' => $this->step,
                        'nextStep' => $this->nextStep,
                        'nextApproverName' => $this->nextApproverName,
                    ]);
    }
}