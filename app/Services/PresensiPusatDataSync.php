<?php

namespace App\Services;

use App\Models\Kehadiran;

class PresensiPusatDataSync
{
    public function __construct(protected PusatDataClient $pusatData) {}

    /**
     * Send an attendance record using the existing Pusat Data payload contract.
     */
    public function kirim(Kehadiran $kehadiran): bool
    {
        $kehadiran->loadMissing(['mahasiswa', 'sesi.kelas']);

        return $this->pusatData->kirimDataPresensi([
            'nim_mahasiswa' => $kehadiran->mahasiswa->username,
            'kode_kelas' => $kehadiran->sesi->kelas->kode,
            'nama_mata_kuliah' => $kehadiran->sesi->kelas->nama_mata_kuliah,
            'status_kehadiran' => $kehadiran->status,
            'waktu' => $kehadiran->waktu_scan->toDateTimeString(),
        ]);
    }
}
