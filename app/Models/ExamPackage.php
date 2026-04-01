<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exam_category_id',
        'title',
        'time_limit',
    ];

    // Relasi ke Atas: Dimiliki oleh 1 Kategori
    public function examCategory()
    {
        return $this->belongsTo(ExamCategory::class);
        return $this->belongsTo(ExamCategory::class)->withTrashed();
    }

    // Relasi ke Bawah: Memiliki banyak Pertanyaan
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    // Relasi ke Bawah: Pernah dikerjakan (menghasilkan banyak nilai)
    public function userResults()
    {
        return $this->hasMany(UserResult::class);
    }

    protected static function booted()
    {
        // Otomatis soft delete semua soal jika paketnya dihapus
        static::deleted(function ($package) {
            $package->questions()->delete();
        });

        // Otomatis pulihkan semua soal jika paketnya dipulihkan
        static::restored(function ($package) {
            $package->questions()->withTrashed()->restore();
        });
    }
}
