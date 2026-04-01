<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Http\Request; // Pastikan ini ada di bagian paling atas file web.php

// ... rute lainnya ...

Route::get('/user/history/{id}/review', function (Request $request, $id) {

    // Keamanan: Cek apakah user benar-benar login, jika tidak, lempar ke halaman login
    if (!$request->user()) {
        return redirect()->route('login');
    }

    // Ambil data nilai, relasi paket, dan jawaban siswa (lengkap dengan soalnya)
    $result = \App\Models\UserResult::with(['examPackage.examCategory', 'userAnswers.question'])
        ->where('user_id', $request->user()->id) // Gunakan $request->user()->id
        ->findOrFail($id);

    return view('user.review', compact('result'));
})->middleware('auth')->name('user.review'); // Tambahkan ->middleware('auth') di sini

// 1. Halaman Utama (Sementara kita arahkan ke halaman bawaan Laravel)
Route::get('/', function () {
    return view('welcome');
});
// Rute untuk menampilkan halaman Form (GET)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::get('/admin/login', function () {
    return view('auth.admin_login');
})->name('admin.login');

// 2. Rute Proses Autentikasi (Menerima data dari Form)
Route::post('/register-process', [AuthController::class, 'register'])->name('register.process');
Route::post('/login-process', [AuthController::class, 'login'])->name('login.process');
Route::post('/admin/login-process', [AuthController::class, 'adminLogin'])->name('admin.login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Rute Khusus USER (Dilindungi oleh Satpam 'auth:web')
Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    // Rute Ujian
    Route::post('/exam/{package}/start', [\App\Http\Controllers\User\ExamController::class, 'startExam'])->name('exam.start');

    // Rute Halaman Pengerjaan Ujian
    Route::get('/exam/play/{result_id}', [\App\Http\Controllers\User\ExamController::class, 'play'])->name('exam.play');

    Route::get('/user/history', function () {
        return view('user.history');
    })->name('user.history');

    Route::get('/user/history/{id}/review', function (Request $request, $id) {

        // Keamanan: Cek apakah user benar-benar login, jika tidak, lempar ke halaman login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Ambil data nilai, relasi paket, dan jawaban siswa (lengkap dengan soalnya)
        $result = \App\Models\UserResult::with(['examPackage.examCategory', 'userAnswers.question'])
            ->where('user_id', $request->user()->id) // Gunakan $request->user()->id
            ->findOrFail($id);

        return view('user.review', compact('result'));
    })->middleware('auth')->name('user.review');

    Route::get('/user/exams', function () {
        return view('user.exams');
    })->name('user.exams');
});

// 4. Rute Khusus ADMIN (Dilindungi oleh Satpam 'auth:admin')
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    // Tambahkan baris ajaib ini: Route Resource untuk CRUD Kategori
    Route::resource('/admin/categories', App\Http\Controllers\Admin\ExamCategoryController::class)->names('admin.categories');
    Route::resource('/admin/packages', App\Http\Controllers\Admin\ExamPackageController::class)->names('admin.packages');
    Route::resource('/admin/questions', App\Http\Controllers\Admin\QuestionController::class)->names('admin.questions');
    // Manajemen User (Siswa/Peserta)
    Route::resource('/admin/users', App\Http\Controllers\Admin\UserController::class)->names('admin.users');
});
