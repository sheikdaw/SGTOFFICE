<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class password_reset_tokens extends Model
{
    use HasFactory;

    protected $table = 'password_reset_tokens'; // Specify the table name

    protected $fillable = [
        'email',
        'token',
    ];
}
