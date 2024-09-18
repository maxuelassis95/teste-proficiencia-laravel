<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable  // Estenda Authenticatable em vez de Model
{
    use HasFactory;

    protected $table = 'admins';
    protected $fillable = [
        'nome',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
