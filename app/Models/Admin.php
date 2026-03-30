<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// Tambahkan library Auth agar Admin bisa login seperti User
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable // Ubah extends Model menjadi Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
    ];
}
