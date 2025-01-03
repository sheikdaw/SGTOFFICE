<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TaxCollector extends Authenticatable
{
    use HasFactory, Notifiable;

    // Add any necessary fields like 'fillable' for mass assignment
    protected $fillable = [
        'name',
        'email',
        'mobile','data_id',
        'password',
        'password_reset_token',
    ];

    // Hidden fields (e.g., passwords) when model is converted to an array or JSON
    protected $hidden = [
        'password',
        'remember_token',
        'password_reset_token',
    ];
    /**
     * Define the relationship with the Data model.
     */

}
