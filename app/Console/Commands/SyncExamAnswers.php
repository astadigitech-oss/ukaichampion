<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\UserAnswer;

class SyncExamAnswers extends Command
{
    protected $signature = 'exam:sync-answers';
    protected $description = 'Memindahkan jawaban dari Redis ke MariaDB';

    public function handle()
    {
        // 1. Cari semua keranjang jawaban ujian di Redis
        $keys = Redis::keys('exam_answers:*');

        if (empty($keys)) {
            $this->info('Tidak ada jawaban baru untuk disinkronkan.');
            return;
        }

        // Jika menggunakan prefix bawaan Laravel, kita bersihkan dulu nama key-nya
        $prefix = config('database.redis.options.prefix', '');

        foreach ($keys as $key) {
            $cleanKey = str_replace($prefix, '', $key);
            $resultId = str_replace('exam_answers:', '', $cleanKey);

            // 2. Ambil semua jawaban siswa tersebut (Format: [question_id => answer])
            $answers = Redis::hgetall($cleanKey);

            // 3. Masukkan ke MariaDB secara massal
            foreach ($answers as $questionId => $answer) {
                UserAnswer::updateOrCreate(
                    ['result_id' => $resultId, 'question_id' => $questionId],
                    ['selected_option' => $answer]
                );
            }

            // 4. Setelah masuk ke Database, hapus keranjang dari Redis agar tidak menumpuk
            Redis::del($cleanKey);
        }

        $this->info('Sinkronisasi selesai! ' . count($keys) . ' kertas ujian telah di-update.');
    }
}
