<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan ini

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes; // Tambahkan SoftDeletes di sini

    // 1. Kolom yang diizinkan untuk diisi (Mass Assignment)
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'is_premium',
        'premium_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_premium' => 'boolean',
            'premium_until' => 'datetime', // Beritahu Laravel ini adalah format waktu
        ];
    }

    // 2. Relasi: Satu User memiliki banyak Transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // 3. Relasi: Satu User memiliki banyak Riwayat Nilai
    public function userResults()
    {
        return $this->hasMany(UserResult::class);
    }
}
