<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $fillable = [
        'corporation_id',
        'corporation_name',
        'ward',
        'zone',
        'image',
        'polygon',
        'line',
        'point',
        'polygondata',
        'mis',
        'qc',
        'pointdata',
        'extend_left',
        'extend_right',
        'extend_top',
        'extend_bottom',
    ];
}
