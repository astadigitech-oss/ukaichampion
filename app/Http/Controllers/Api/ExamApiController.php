<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ExamApiController extends Controller
{
    public function saveAnswer(Request $request)
    {
        // 1. Validasi data lemparan dari Javascript
        $request->validate([
            'result_id' => 'required|integer',
            'question_id' => 'required|integer',
            'answer' => 'required|string|max:1',
        ]);

        // 2. SIMPAN KE REDIS (Bukan ke MariaDB!)
        // Format kuncinya: exam_answers:{result_id}
        // Di dalamnya berupa keranjang (Hash) berisi {question_id : answer}
        $redisKey = 'exam_answers:' . $request->result_id;

        Redis::hset($redisKey, $request->question_id, $request->answer);

        // 3. Beri tahu Javascript bahwa penyimpanan sukses (Hanya butuh 0.001 detik)
        return response()->json(['status' => 'success', 'message' => 'Tersimpan']);
    }
}
