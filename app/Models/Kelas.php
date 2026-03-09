<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tahun_ajar_id',
        'wali_guru_id'
    ];

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class);
    }

    public function waliGuru()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaKelas::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
