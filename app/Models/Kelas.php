<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

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
        return $this->belongsTo(User::class, 'wali_guru_id');
    }
}
