<?php

namespace App\Observers;

use App\Mail\AkunDisetujuiMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function updated(User $user): void
    {
        if (
            $user->wasChanged('status') &&
            $user->status === 'aktif' &&
            $user->getOriginal('status') !== 'aktif'
        ) {
            try {
                Mail::to($user->email)->send(new AkunDisetujuiMail($user));
            } catch (\Exception $e) {
                Log::error("Gagal kirim email aktivasi ke {$user->email}: " . $e->getMessage());
            }
        }
    }
}
