<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnggotaKelas extends Model
{
    use HasFactory;

    protected $table = 'anggota_kelas';

    public $timestamps = false;

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
        return $this->belongsTo(User::class , 'murid_id');
    }

    
}
