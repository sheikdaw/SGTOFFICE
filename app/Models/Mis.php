<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mis extends Model
{
    protected $fillable = [
        'assessment',
        'old_assessment',
        'number_floor',
        'new_address',
        'building_usage',
        'construction_type',
        'road_name',
        'phone',
        'building_type',
        'ward',
        'owner_name',
        'old_door_no',
        'new_door_no',
        'plot_area',
        'watertax',
        'halfyeartax',
        'balance',
    ];
}
