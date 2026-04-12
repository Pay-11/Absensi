<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas'
    ];

    public function murid()
    {
        return $this->belongsToMany(User::class , 'anggota_kelas', 'kelas_id', 'murid_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class , 'tahun_ajar_id');
    }
    public function waliGuru()    {
        return $this->belongsTo(User::class , 'wali_guru_id');    
    }
}
