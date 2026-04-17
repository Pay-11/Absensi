<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaKelas extends Model
{
    use HasFactory;
    
    public const UPDATED_AT = null; // doesn't have updated_at in schema

    protected $fillable = [
        'kelas_id',
        'murid_id'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }
}
