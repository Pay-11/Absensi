<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiAbsen extends Model
{
    protected $table = 'sesi_absen';

    public $timestamps = false;

    protected $fillable = [
        'jadwal_id',
        'tanggal',
        'token_qr',
        'expired_at',
        'dibuka_oleh',
        'dibuka_pada',
        'ditutup_pada'
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
