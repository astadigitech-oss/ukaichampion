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
}
