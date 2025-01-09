<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    // If you want to define a custom table name (useful when the plural name is different)
    protected $table = 'attendences';  // This line is optional because Laravel automatically assumes the plural form

    // Define which columns are mass assignable
    protected $fillable = [
        'name',
        'data',
        'in_time',
        'out_time',
        'ward',
        'location',
    ];

    // If you're using a date or time format for some fields
    protected $dates = [
        'data',
        'created_at',
        'updated_at',
    ];

    // If location is a JSON column, you can access it as an array.
    protected $casts = [
        'location' => 'array',
    ];
}
