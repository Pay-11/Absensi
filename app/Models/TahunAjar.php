<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjar extends Model
{
    protected $table = 'tahun_ajar';

    protected $fillable = [
        'nama',
        'aktif'
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
