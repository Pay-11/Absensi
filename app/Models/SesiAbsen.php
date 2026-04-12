<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesiAbsen extends Model
{
    use HasFactory;

    protected $table = 'sesi_absen';

    protected $fillable = [
        'jadwal_id',
        'tanggal',
        'token_qr',
        'dibuka_oleh',
        'dibuka_pada'
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function pembuka()
    {
        return $this->belongsTo(User::class , 'dibuka_oleh');
    }
}
