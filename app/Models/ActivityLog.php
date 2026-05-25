<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'role',
        'module',
        'action',
        'description',
        'ip_address',
        'user_agent'
    ];

        public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
