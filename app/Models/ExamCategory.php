<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    // Relasi: Satu Kategori memiliki banyak Paket Soal
    public function examPackages()
    {
        return $this->hasMany(ExamPackage::class);
    }
}
