<?php

use App\Http\Controllers\Api\AttendanceController;
use Illuminate\Support\Facades\Route;

// API Routes untuk Sistem Presensi
// Diakses dari sistem lain untuk cek eligibility mahasiswa

Route::prefix('mahasiswa')->group(function () {
    Route::get('{nim}/attendance-summary', [AttendanceController::class, 'getAttendanceSummary']);
    Route::get('{nim}/attendance-details', [AttendanceController::class, 'getAttendanceDetails']);
});
