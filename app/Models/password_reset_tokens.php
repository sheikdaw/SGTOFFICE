<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class password_reset_tokens extends Model
{
    use HasFactory;

    protected $table = 'password_reset_tokens';

    public $timestamps = false; // Disable timestamps if they're not used

    protected $fillable = [
        'email',
        'token',
    ];

    protected $primaryKey = 'email'; // Use email as the primary key
    public $incrementing = false; // Set to false if the primary key is not an integer
    protected $keyType = 'string';
}
