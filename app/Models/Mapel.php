<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'mapel';

    protected $fillable = [
        'nama_mapel',
        'kode_mapel'
    ];

    public function guru()
    {
        return $this->belongsToMany(User::class , 'guru_mapel', 'mapel_id', 'guru_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
