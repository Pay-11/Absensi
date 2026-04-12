<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruMapel extends Model
{
    protected $table = 'guru_mapel';

    public $timestamps = false;

    protected $fillable = [
        'guru_id',
        'mapel_id'
    ];

    public function guru()
    {
        return $this->belongsTo(User::class,'guru_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
}
