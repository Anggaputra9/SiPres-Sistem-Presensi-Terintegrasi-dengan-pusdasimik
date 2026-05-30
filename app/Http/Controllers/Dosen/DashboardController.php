<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\SesiPresensi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $dosen = auth()->user();

        $jumlahKelas = Kelas::where('dosen_id', $dosen->id)->count();
        $jumlahSesiAktif = SesiPresensi::where('dosen_id', $dosen->id)
            ->where('ditutup', false)
            ->where('mulai', '<=', now())
            ->where('selesai', '>=', now())
            ->count();
        $totalKehadiranHariIni = \App\Models\Kehadiran::whereHas('sesi', function ($q) use ($dosen) {
            $q->where('dosen_id', $dosen->id);
        })->whereDate('waktu_scan', today())->count();

        $sesiTerbaru = SesiPresensi::with('kelas')
            ->where('dosen_id', $dosen->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dosen.dashboard', compact(
            'jumlahKelas',
            'jumlahSesiAktif',
            'totalKehadiranHariIni',
            'sesiTerbaru',
        ));
    }
}
