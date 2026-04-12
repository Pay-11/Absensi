<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    public $timestamps = false;

    protected $fillable = [
        'sesi_absen_id',
        'murid_id',
        'status',
        'waktu_scan'
    ];

    public function sesi()
    {
        return $this->belongsTo(SesiAbsen::class, 'sesi_absen_id');
    }

    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }
}

