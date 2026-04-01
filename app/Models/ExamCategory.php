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

    protected static function booted()
    {
        // Ketika kategori dihapus (soft delete), hapus juga semua paketnya
        static::deleted(function ($category) {
            $category->examPackages()->delete();
        });

        // (Opsional) Jika kategori dipulihkan, pulihkan juga paketnya
        static::restored(function ($category) {
            $category->examPackages()->withTrashed()->restore();
        });
    }
}
