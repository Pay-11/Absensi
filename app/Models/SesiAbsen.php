<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiAbsen extends Model
{
    use HasFactory;

    protected $table = 'sesi_absen';
    public const UPDATED_AT = null;

    protected $fillable = [
        'jadwal_id',
        'tanggal',
        'tipe',
        'token_qr',
        'dibuka_oleh',
        'dibuka_pada',
        'is_closed'
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function dibukaOleh()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }
}
