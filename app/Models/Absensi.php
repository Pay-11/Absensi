<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    public const UPDATED_AT = null;

    protected $fillable = [
        'sesi_absen_id',
        'murid_id',
        'status',
        'waktu_scan'
    ];

    public function sesiAbsen()
    {
        return $this->belongsTo(SesiAbsen::class);
    }

    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }
}
