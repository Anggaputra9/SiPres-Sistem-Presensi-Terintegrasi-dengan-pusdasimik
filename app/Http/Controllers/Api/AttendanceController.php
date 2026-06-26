<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Get attendance summary for a student
     * GET /api/mahasiswa/{username}/attendance-summary
     */
    public function getAttendanceSummary(string $username): JsonResponse
    {
        $mahasiswa = User::where('username', $username)
            ->where('role', User::ROLE_MAHASISWA)
            ->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404);
        }

        // Hitung total sesi presensi yang mahasiswa ikuti (dari kelas yang diikuti)
        $kelasIds = $mahasiswa->kelasDiikuti()->pluck('kelas.id');
        
        $totalSesi = DB::table('sesi_presensi')
            ->whereIn('kelas_id', $kelasIds)
            ->where('waktu_mulai', '<=', now())
            ->count();

        // Hitung kehadiran mahasiswa (hadir + terlambat)
        $attendedSesi = DB::table('kehadiran')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        // Hitung persentase
        $percentage = $totalSesi > 0 ? ($attendedSesi / $totalSesi) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'nim' => $mahasiswa->username,
                'nama' => $mahasiswa->nama,
                'total_sesi' => $totalSesi,
                'sesi_hadir' => $attendedSesi,
                'sesi_tidak_hadir' => $totalSesi - $attendedSesi,
                'persentase_kehadiran' => round($percentage, 2),
                'memenuhi_syarat' => $percentage >= 75,
                'minimum_required' => 75,
            ]
        ]);
    }

    /**
     * Get detailed attendance records for a student
     * GET /api/mahasiswa/{username}/attendance-details
     */
    public function getAttendanceDetails(string $username): JsonResponse
    {
        $mahasiswa = User::where('username', $username)
            ->where('role', User::ROLE_MAHASISWA)
            ->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404);
        }

        $kehadiran = $mahasiswa->kehadiran()
            ->with(['sesi.kelas'])
            ->orderBy('waktu_scan', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->waktu_scan,
                    'mata_kuliah' => $item->sesi->kelas->nama ?? '-',
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                    'catatan' => $item->catatan,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'nim' => $mahasiswa->username,
                'nama' => $mahasiswa->nama,
                'kehadiran' => $kehadiran,
            ]
        ]);
    }
}
