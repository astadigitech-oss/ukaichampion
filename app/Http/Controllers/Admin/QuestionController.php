<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\ExamPackage;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // Method index biarkan kosong atau arahkan ke Livewire/Volt yang sudah kita buat
    public function index()
    {
        return redirect()->route('admin.packages.index');
    }

    public function create(Request $request)
    {
        // Tangkap package_id dari URL (jika ada)
        $selectedPackageId = $request->query('package_id');

        $packages = ExamPackage::latest()->get();
        return view('admin.questions.create', compact('packages', 'selectedPackageId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_package_id' => 'required|exists:exam_packages,id',
            'question_text'   => 'required', // Hapus 'string' agar Quill Editor lancar
            'option_a'        => 'required', // Minimal A wajib isi
            'option_b'        => 'required', // Minimal B wajib isi
            'option_c'        => 'nullable',
            'option_d'        => 'nullable',
            'option_e'        => 'nullable',
            'correct_answer'  => 'required|in:A,B,C,D,E',
            'explanation'     => 'nullable',
        ]);

        \App\Models\Question::create($validated);

        // Gunakan variabel $validated agar lebih konsisten
        if ($request->input('action') === 'save_and_add') {
            return redirect()->route('admin.questions.create', ['package_id' => $validated['exam_package_id']])
                ->with('success', 'Soal sebelumnya berhasil disimpan! Silakan ketik soal berikutnya.');
        }

        return redirect()->route('admin.packages.show', $validated['exam_package_id'])
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function edit(string $id)
    {
        // Cari soal yang mau diedit
        $question = Question::findOrFail($id);

        // Ambil data paket untuk dropdown
        $packages = ExamPackage::latest()->get();

        // Buka halaman form edit
        return view('admin.questions.edit', compact('question', 'packages'));
    }

    public function update(Request $request, string $id)
    {
        // Validasi data baru
        $validated = $request->validate([
            'exam_package_id' => 'required|exists:exam_packages,id',
            'question_text' => 'required|string',
            'option_a' => 'nullable|string',
            'option_b' => 'nullable|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'option_e' => 'nullable|string',
            'correct_answer' => 'required|in:A,B,C,D,E',
            'explanation' => 'nullable|string',
        ]);

        // Cari soal lama dan perbarui dengan data baru
        $question = Question::findOrFail($id);
        $question->update($validated);

        // Kembalikan ke Ruang Kelola Soal paket tersebut
        return redirect()->route('admin.packages.show', $question->exam_package_id)
            ->with('success', 'Perubahan soal berhasil disimpan!');
    }

    public function destroy(string $id)
    {
        // Cari soalnya
        $question = Question::findOrFail($id);

        // Simpan ID paketnya dulu sebelum soalnya dihapus (untuk arah kembali)
        $packageId = $question->exam_package_id;

        // Hapus soal dari database
        $question->delete();

        // Kembalikan Admin ke Ruang Kelola Soal paket tersebut
        return redirect()->route('admin.packages.show', $packageId)
            ->with('success', 'Satu soal berhasil dihapus dari paket!');
    }
}
