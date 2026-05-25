<?php
// Model Karyawan
namespace App\Models;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Departemen;
use App\Models\Gaji;
use App\Models\Jabatan;
use App\Models\JatahCuti;
use App\Models\KaryawanShiftPattern;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $fillable = [
        'nip',
        'user_id',
        'jabatan_id',
        'departemen_id',
        'foto_profil',
        'status',
        'wajah_terdaftar',
        'no_telepon',
        'no_telepon_tambahan',
        'alamat',
        // Personal Data
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_pernikahan',
        'golongan_darah',
        'agama',
        // Identity & Address
        'nik',
        'alamat_ktp',
        'kode_pos',
        'alamat_tinggal',
        'no_paspor',
        'masa_berlaku_paspor',
    ];

    protected $casts = [
        'wajah_terdaftar' => 'boolean',
        'tanggal_lahir' => 'date',
        'masa_berlaku_paspor' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

        public function jatahCuti()
    {
        return $this->hasMany(JatahCuti::class);
    }

    public function gaji()
    {
        return $this->hasMany(Gaji::class);
    }


    public function shiftPatterns()
    {
        return $this->hasMany(KaryawanShiftPattern::class);
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class, 'karyawan_id');
    }

    public function getFaceDescriptorArrayAttribute()
    {
        return $this->face_descriptor ? json_decode($this->face_descriptor, true) : null;
    }
}
