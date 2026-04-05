<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
use App\Models\ExamCategory; // Wajib dipanggil karena kita butuh data kategori
use Illuminate\Http\Request;

class ExamPackageController extends Controller
{
    // 1. Tampilkan Daftar Paket Soal
    // Jangan lupa pastikan 'use Illuminate\Http\Request;' ada di bagian atas file

    public function index(\Illuminate\Http\Request $request)
    {
        // Tangkap data dari form filter
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $type = $request->input('type'); // Filter baru untuk Freemium

        $query = \App\Models\ExamPackage::with('examCategory');

        // 1. Filter Pencarian Nama
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // 2. Filter Kategori
        if ($categoryId) {
            $query->where('exam_category_id', $categoryId);
        }

        // 3. Filter Premium atau Gratis
        if ($type === 'premium') {
            $query->where('is_premium', true);
        } elseif ($type === 'gratis') {
            $query->where('is_premium', false);
        }

        $query = \App\Models\ExamPackage::with('examCategory')->withCount('questions');
        // Eksekusi data (withQueryString agar saat pindah halaman, filter tidak hilang)
        $packages = $query->latest()->paginate(10)->withQueryString();

        // Ambil data kategori untuk dropdown
        $categories = \App\Models\ExamCategory::orderBy('name')->get();

        return view('admin.packages.index', compact('packages', 'categories', 'search', 'categoryId', 'type'));
    }

    // 2. Tampilkan Form Tambah
    public function create()
    {
        // Ambil semua kategori untuk ditampilkan di dalam <select> dropdown
        $categories = ExamCategory::all();
        return view('admin.packages.create', compact('categories'));
    }

    // 3. Simpan Data ke Database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_category_id' => 'required|exists:exam_categories,id',
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
        ]);

        // Tangkap status checkbox: Jika dicentang bernilai true, jika tidak bernilai false
        $validated['is_premium'] = $request->has('is_premium');

        \App\Models\ExamPackage::create($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Paket ujian berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        // Cari paket berdasarkan ID, sekalian ambil data kategori dan soal-soalnya
        $package = ExamPackage::with(['examCategory', 'questions'])->findOrFail($id);

        // Buka halaman Ruang Kelola Soal
        return view('admin.packages.show', compact('package'));
    }
    // 4. Tampilkan Form Edit
    public function edit(ExamPackage $package)
    {
        $categories = ExamCategory::all(); // Butuh data kategori untuk dropdown
        return view('admin.packages.edit', compact('package', 'categories'));
    }

    // 5. Update Data di Database
    public function update(Request $request, $id)
    {
        $package = \App\Models\ExamPackage::findOrFail($id);

        $validated = $request->validate([
            'exam_category_id' => 'required|exists:exam_categories,id',
            'title' => 'required|string|max:255',
            'time_limit' => 'required|integer|min:1',
        ]);

        // Tangkap status saklar saat diedit (jika dicentang = true, jika mati = false)
        $validated['is_premium'] = $request->has('is_premium');

        $package->update($validated);

        return redirect()->route('admin.packages.index')->with('success', 'Paket ujian berhasil diperbarui.');
    }

    // 6. Hapus Data (Soft Delete)
    public function destroy(ExamPackage $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Paket soal berhasil dihapus!');
    }
}
