<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserResult extends Model
{
    protected $fillable = [
        'user_id',
        'exam_package_id',
        'attempt_number',
        'score',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'finished_at' => 'datetime',
            'ends_at' => 'datetime', // <--- TAMBAHKAN INI WAJIB!
        ];
    }

    // Relasi ke Atas: Milik 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Atas: Hasil dari 1 Paket Soal
    public function examPackage()
    {
        return $this->belongsTo(ExamPackage::class);
        return $this->belongsTo(ExamPackage::class)->withTrashed();
    }

    // Relasi ke Bawah: Memiliki banyak detail jawaban. 
    // PERHATIKAN: Kita harus mendefinisikan 'result_id' secara eksplisit di sini.
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'result_id');
    }
}
