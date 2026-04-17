<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlexibilityItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'point_cost',
        'type',
        'max_late_minutes',
        'stock_limit'
    ];
}
