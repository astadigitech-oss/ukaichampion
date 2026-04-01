<?php

use Livewire\Volt\Component;
use App\Models\UserResult;
use App\Models\UserAnswer;
use App\Models\Question;
use App\Models\ExamPackage;
use Carbon\Carbon;

new class extends Component {
    public $result_id;
    public $exam_package_id;
    public $questions;
    public $currentQuestionIndex = 0;
    public $answers = [];
    public $endTime;

    public function mount($result_id)
    {
        $this->result_id = $result_id;
        $result = UserResult::with('examPackage')->findOrFail($result_id);

        if ($result->finished_at) {
            return redirect()->route('user.dashboard');
        }

        $this->exam_package_id = $result->exam_package_id;
        $this->questions = Question::where('exam_package_id', $this->exam_package_id)->get();

        // LOGIKA REFRESH: Ambil nomor soal terakhir dari session jika ada
        $this->currentQuestionIndex = session('last_q_' . $this->result_id, 0);

        $durationMinutes = $result->examPackage->time_limit;
        $this->endTime = $result->created_at->addMinutes($durationMinutes)->toIso8601String();

        $existingAnswers = UserAnswer::where('result_id', $result_id)->get();
        foreach ($existingAnswers as $ans) {
            $this->answers[$ans->question_id] = $ans->selected_option;
        }
    }

    // Fungsi untuk menyimpan posisi soal terakhir ke session setiap kali pindah nomor
    public function updateSessionIndex()
    {
        session(['last_q_' . $this->result_id => $this->currentQuestionIndex]);
    }

    public function answerQuestion($questionId, $selectedOption)
    {
        $this->answers[$questionId] = $selectedOption;
        UserAnswer::updateOrCreate(['result_id' => $this->result_id, 'question_id' => $questionId], ['selected_option' => $selectedOption]);
    }

    public function finishExam()
    {
        // Hapus session index saat ujian benar-benar selesai agar tidak nyangkut di ujian berikutnya
        session()->forget('last_q_' . $this->result_id);

        $result = UserResult::findOrFail($this->result_id);
        $totalQuestions = count($this->questions);
        $correctAnswersCount = 0;

        foreach ($this->questions as $question) {
            $userAns = UserAnswer::where('result_id', $this->result_id)->where('question_id', $question->id)->first();

            if ($userAns) {
                $isCorrect = $userAns->selected_option === $question->correct_answer;
                if ($isCorrect) {
                    $correctAnswersCount++;
                }
                $userAns->update(['is_correct' => $isCorrect]);
            }
        }

        $finalScore = $totalQuestions > 0 ? ($correctAnswersCount / $totalQuestions) * 100 : 0;
        $result->update(['score' => $finalScore, 'finished_at' => now()]);

        // PERBAIKAN DI SINI: Ubah user.dashboard menjadi user.exams
        return redirect()
            ->route('user.exams')
            ->with('success', 'Ujian Selesai! Skor Anda: ' . number_format($finalScore, 1));
    }
    // Tambahkan pemanggilan updateSessionIndex di setiap navigasi
    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
            $this->updateSessionIndex();
        }
    }
    public function prevQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->updateSessionIndex();
        }
    }
    public function jumpToQuestion($index)
    {
        $this->currentQuestionIndex = $index;
        $this->updateSessionIndex();
    }
}; ?>

<div x-data="timerData()" x-init="startTimer()">
    @if (count($questions) > 0)
        @php $currentQ = $questions[$currentQuestionIndex]; @endphp

        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-full md:w-3/4 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-blue-50 p-4 border-b flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('user.dashboard') }}"
                            onclick="return confirm('Keluar dari ruang ujian? Anda tetap bisa melanjutkan selama waktu masih tersedia.')"
                            class="text-gray-500 hover:text-red-600 font-bold transition-colors">
                            ✕ Keluar
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h3 class="text-lg font-bold text-blue-800">Soal No. {{ $currentQuestionIndex + 1 }}</h3>
                    </div>

                    <div
                        class="flex items-center gap-2 bg-red-600 text-white font-mono font-bold px-4 py-1 rounded-full shadow-sm">
                        <span>⏱️</span>
                        <span x-text="displayTime">00:00:00</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach (['A', 'B', 'C', 'D', 'E'] as $opt)
                        @php $optionField = 'option_' . strtolower($opt); @endphp
                        @if ($currentQ->$optionField)
                            <label wire:key="opt-{{ $currentQ->id }}-{{ $opt }}"
                                class="flex items-start gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all {{ isset($answers[$currentQ->id]) && $answers[$currentQ->id] == $opt ? 'bg-blue-50 border-blue-500' : 'hover:bg-gray-50 border-gray-100' }}">
                                <div class="pt-1">

                                    <input type="radio" name="jawaban_{{ $currentQ->id }}"
                                        wire:click="answerQuestion({{ $currentQ->id }}, '{{ $opt }}')"
                                        {{ isset($answers[$currentQ->id]) && $answers[$currentQ->id] == $opt ? 'checked' : '' }}
                                        class="w-5 h-5 text-blue-600">

                                </div>
                                <div class="flex gap-3"><span
                                        class="font-bold text-gray-700">{{ $opt }}.</span>
                                    {!! $currentQ->$optionField !!}</div>
                            </label>
                        @endif
                    @endforeach
                </div>

                <div class="bg-gray-50 p-4 border-t flex justify-between">
                    <button wire:click="prevQuestion"
                        class="px-6 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-lg transition-colors {{ $currentQuestionIndex == 0 ? 'invisible' : '' }}">⬅️
                        Sebelumnya</button>

                    @if ($currentQuestionIndex < count($questions) - 1)
                        <button wire:click="nextQuestion"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm">Selanjutnya
                            ➡️</button>
                    @else
                        <button wire:click="finishExam"
                            wire:confirm="Apakah Anda yakin ingin mengakhiri ujian ini? Nilai akan segera dihitung."
                            class="px-8 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg shadow-lg">
                            ✅ Selesai & Kirim Jawaban
                        </button>
                    @endif
                </div>
            </div>

            <div class="w-full md:w-1/4">
                <div class="bg-white p-6 rounded-xl shadow-sm border sticky top-6">
                    <h4 class="font-bold text-gray-800 mb-4 text-center border-b pb-2">Navigasi Soal</h4>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach ($questions as $index => $q)
                            <button wire:click="jumpToQuestion({{ $index }})"
                                class="w-full aspect-square flex items-center justify-center font-bold text-sm border rounded transition-all {{ isset($answers[$q->id]) ? 'bg-blue-500 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-600' }} {{ $index === $currentQuestionIndex ? 'ring-2 ring-blue-800 ring-offset-2' : '' }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function timerData() {
            return {
                endTime: new Date("{{ $endTime }}").getTime(),
                displayTime: '00:00:00',
                startTimer() {
                    let interval = setInterval(() => {
                        let now = new Date().getTime();
                        let distance = this.endTime - now;
                        if (distance < 0) {
                            clearInterval(interval);
                            this.displayTime = "WAKTU HABIS";
                            @this.finishExam();
                            return;
                        }
                        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        this.displayTime = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" +
                            minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
                    }, 1000);
                }
            }
        }
    </script>
</div>
