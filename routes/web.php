<?php

use App\Http\Controllers\GuruController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JadwalKonselingController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\ManajemenUserController;
use App\Http\Controllers\OrangtuaController;
use App\Http\Controllers\PermohonanKonselingController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\KriteriaController as ApiKriteriaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {

    // Users
    Route::resource('siswa', SiswaController::class)->except(['show']);
    Route::resource('guru', GuruController::class);
    Route::resource('orangtua', OrangtuaController::class);

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/jadwal-konseling', [JadwalKonselingController::class, 'index']);
    Route::get('/riwayat-konseling', [RiwayatController::class, 'index']);
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/delete', [UserController::class, 'destroy'])->name('users.destroy');

    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');


    // Kelas & Tahun Akademik
    Route::resource('kelas', KelasController::class);
    Route::resource('tahun-akademik', TahunAkademikController::class);

    // Permohonan Konseling
    Route::resource('permohonan-konseling', PermohonanKonselingController::class)->only(['index', 'store', 'create']);
    Route::patch('permohonan-konseling/approve/{id}', [PermohonanKonselingController::class, 'approve'])->name('permohonan-konseling.approve');
    Route::patch('permohonan-konseling/reject/{id}', [PermohonanKonselingController::class, 'reject'])->name('permohonan-konseling.reject');
    Route::patch('permohonan-konseling/complete/{id}', [PermohonanKonselingController::class, 'complete'])->name('permohonan-konseling.complete');

    // Kriteria Management (Admin Panel)
    Route::resource('kriteria', KriteriaController::class);
    Route::get('kriteria/{kriteria}/sub-kriteria', [KriteriaController::class, 'subKriteriaIndex'])->name('kriteria.sub-kriteria.index');
    Route::post('kriteria/{kriteria}/sub-kriteria', [KriteriaController::class, 'subKriteriaStore'])->name('kriteria.sub-kriteria.store');
    Route::put('kriteria/{kriteria}/sub-kriteria/{subKriteria}', [KriteriaController::class, 'subKriteriaUpdate'])->name('kriteria.sub-kriteria.update');
    Route::delete('kriteria/{kriteria}/sub-kriteria/{subKriteria}', [KriteriaController::class, 'subKriteriaDestroy'])->name('kriteria.sub-kriteria.destroy');

    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::get('/laporan/pdf', [LaporanController::class, 'cetakPdf'])->name('laporan.pdf');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::patch('/permohonan-konseling/edit-jadwal/{id}', [PermohonanKonselingController::class, 'updateJadwal'])
        ->name('permohonan-konseling.edit-jadwal');

    Route::get('/manajemen-user', [ManajemenUserController::class, 'index'])
        ->name('manajemen-user.index');

    Route::post('/manajemen-user/{user}/reset-password', [ManajemenUserController::class, 'resetPassword'])
        ->name('manajemen-user.reset-password');

    // API Routes (No middleware restriction)
    Route::get('/api/kriteria', [ApiKriteriaController::class, 'index'])->name('api.kriteria.index');
});
