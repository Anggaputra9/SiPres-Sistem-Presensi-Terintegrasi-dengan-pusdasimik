<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\SesiPresensi;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalDosen = User::where('role', User::ROLE_DOSEN)->count();
        $totalMahasiswa = User::where('role', User::ROLE_MAHASISWA)->count();
        $totalKelas = Kelas::count();
        $totalSesi = SesiPresensi::count();
        $totalKehadiran = Kehadiran::count();

        $sesiAktif = SesiPresensi::where('ditutup', false)
            ->where('mulai', '<=', now())
            ->where('selesai', '>=', now())
            ->count();

        $recentUsers = User::whereIn('role', [User::ROLE_DOSEN, User::ROLE_MAHASISWA])
            ->latest()
            ->take(10)
            ->get();

        $recentSesi = SesiPresensi::with('kelas', 'dosen')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalDosen',
            'totalMahasiswa',
            'totalKelas',
            'totalSesi',
            'totalKehadiran',
            'sesiAktif',
            'recentUsers',
            'recentSesi'
        ));
    }
}
