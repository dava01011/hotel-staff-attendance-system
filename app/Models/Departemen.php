<?php
// Model Departemen
namespace App\Models;

use App\Models\Karyawan;
use App\Models\AjukanShift;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemen';

    protected $fillable = [
        'nama',
    ];

    public function ajukanShift()
    {
        return $this->hasMany(AjukanShift::class);
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }
}
