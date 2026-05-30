<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Dosen\KelasController;
use App\Http\Controllers\Dosen\SesiPresensiController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\PresensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        return redirect()->route($user->isDosen() ? 'dosen.dashboard' : 'mahasiswa.dashboard');
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenDashboardController::class, 'index'])->name('dashboard');

    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
    Route::post('kelas/{kelas}/enrol', [KelasController::class, 'enrol'])->name('kelas.enrol');
    Route::delete('kelas/{kelas}/unenrol/{mahasiswa}', [KelasController::class, 'unenrol'])->name('kelas.unenrol');

    Route::resource('sesi', SesiPresensiController::class)->except(['edit', 'update']);
    Route::post('sesi/{sesi}/tutup', [SesiPresensiController::class, 'tutup'])->name('sesi.tutup');
    Route::post('sesi/{sesi}/buka', [SesiPresensiController::class, 'bukaUlang'])->name('sesi.buka');
    Route::post('sesi/{sesi}/manual', [SesiPresensiController::class, 'tandaiManual'])->name('sesi.manual');
});

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/kelas', [PresensiController::class, 'kelas'])->name('kelas.index');

    Route::get('/presensi', [PresensiController::class, 'form'])->name('presensi.form');
    Route::post('/presensi', [PresensiController::class, 'submit'])->name('presensi.submit');
    Route::get('/presensi/riwayat', [PresensiController::class, 'riwayat'])->name('presensi.riwayat');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/test', [SettingsController::class, 'testConnection'])->name('settings.test');

    Route::resource('users', AdminUserController::class);
});
