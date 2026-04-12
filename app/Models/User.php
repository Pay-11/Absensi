<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nisn',
        'nip'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }

    public function isMurid()
    {
        return $this->role === 'murid';
    }

    // murid ikut kelas
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class , 'anggota_kelas', 'murid_id', 'kelas_id');
    }

    // guru mengajar mapel
    public function mapel()
    {
        return $this->belongsToMany(Mapel::class , 'guru_mapel', 'guru_id', 'mapel_id');
    }

    // jadwal mengajar
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class , 'guru_id');
    }

    // absensi murid
    public function absensi()
    {
        return $this->hasMany(Absensi::class , 'murid_id');
    }

    // membuka sesi absen
    public function sesiDibuka()
    {
        return $this->hasMany(SesiAbsen::class , 'dibuka_oleh');
    }

    // evaluator
    public function assessmentsGiven()
    {
        return $this->hasMany(Assessment::class , 'evaluator_id');
    }

    // yang dinilai
    public function assessmentsReceived()
    {
        return $this->hasMany(Assessment::class , 'evaluatee_id');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
