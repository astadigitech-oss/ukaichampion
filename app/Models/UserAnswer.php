<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    protected $fillable = [
        'result_id',
        'question_id',
        'selected_option',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    // Relasi ke Atas: Milik 1 Riwayat Nilai
    public function userResult()
    {
        return $this->belongsTo(UserResult::class, 'result_id');
    }

    // Relasi ke Atas: Menjawab 1 Pertanyaan
    public function question()
    {
        // Tetap baca teks soal dan kunci jawaban dari "tong sampah"
        // agar halaman Review milik siswa tidak error
        return $this->belongsTo(Question::class)->withTrashed();
    }
}
