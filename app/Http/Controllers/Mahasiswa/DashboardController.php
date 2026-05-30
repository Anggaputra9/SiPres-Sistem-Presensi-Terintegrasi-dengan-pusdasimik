<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $mahasiswa = auth()->user();

        $jumlahKelas = $mahasiswa->kelasDiikuti()->count();

        $totalHadir = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
        $totalAlpha = Kehadiran::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'alpha')
            ->count();

        $riwayat = Kehadiran::with('sesi.kelas')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->latest('waktu_scan')
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact(
            'jumlahKelas',
            'totalHadir',
            'totalAlpha',
            'riwayat',
        ));
    }
}
