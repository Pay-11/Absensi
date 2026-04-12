<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianSikap extends Model
{
    protected $fillable = [
        'siswa_id',
        'guru_id',
        'sikap',
        'keterangan',
        'tanggal',
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
