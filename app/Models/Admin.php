<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    // Add any necessary fields like 'fillable' for mass assignment
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Hidden fields (e.g., passwords) when model is converted to an array or JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
