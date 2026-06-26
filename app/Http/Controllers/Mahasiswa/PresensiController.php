<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\SesiPresensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\PusatDataClient;

class PresensiController extends Controller
{
    public function form(): View
    {
        return view('mahasiswa.presensi.scan');
    }

    // Ubah fungsi submit menjadi seperti ini:
    public function submit(Request $request, PusatDataClient $client): RedirectResponse
    {
        $data = $request->validate([
            'kode_referal' => 'required|string|max:32',
        ]);

        $kode = strtoupper(trim($data['kode_referal']));
        $sesi = SesiPresensi::where('kode_referal', $kode)->first();

        if (! $sesi) {
            return back()->withErrors(['kode_referal' => 'Kode presensi tidak dikenali.'])
                ->withInput();
        }

        $mahasiswa = auth()->user();
        
        // Cek permissions mahasiswa dari Pusat Data
        $permissions = $client->checkMahasiswaPermissions($mahasiswa->username);
        if (!$permissions || !($permissions['permissions']['can_attend'] ?? false)) {
            $status = $permissions['status_label'] ?? 'tidak aktif';
            return back()->withErrors([
                'kode_referal' => "Anda tidak dapat melakukan presensi karena status: {$status}"
            ])->withInput();
        }
        $terdaftar = $sesi->kelas->mahasiswa()->where('mahasiswa_id', $mahasiswa->id)->exists();

        if (! $terdaftar) {
            return back()->withErrors(['kode_referal' => 'Anda tidak terdaftar di kelas '.$sesi->kelas->kode.'.'])
                ->withInput();
        }

        if ($sesi->ditutup) {
            return back()->withErrors(['kode_referal' => 'Sesi presensi sudah ditutup.'])
                ->withInput();
        }

        $now = now();
        if ($now->lt($sesi->mulai)) {
            return back()->withErrors(['kode_referal' => 'Sesi presensi belum dimulai.'])
                ->withInput();
        }
        if ($now->gt($sesi->selesai)) {
            return back()->withErrors(['kode_referal' => 'Sesi presensi sudah berakhir.'])
                ->withInput();
        }

        $sudah = Kehadiran::where('sesi_presensi_id', $sesi->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($sudah) {
            return redirect()->route('mahasiswa.presensi.riwayat')
                ->with('warning', 'Anda sudah melakukan presensi untuk sesi ini.');
        }

        $batasTerlambat = $sesi->mulai->copy()->addMinutes(15);
        $status = $now->gt($batasTerlambat) ? 'terlambat' : 'hadir';

        Kehadiran::create([
            'sesi_presensi_id' => $sesi->id,
            'mahasiswa_id' => $mahasiswa->id,
            'status' => $status,
            'waktu_scan' => $now,
        ]);

        // === PUSH KE PUSAT DATA ===
        $client->kirimDataPresensi([
            'nim_mahasiswa' => $mahasiswa->username, // Kolom username menyimpan NIM
            'kode_kelas' => $sesi->kelas->kode,
            'nama_mata_kuliah' => $sesi->kelas->nama_mata_kuliah,
            'status_kehadiran' => $status,
            'waktu' => $now->toDateTimeString(),
        ]);
        // ==========================

        return redirect()->route('mahasiswa.presensi.riwayat')
            ->with('success', 'Presensi tercatat ('.$status.') untuk '.$sesi->kelas->nama_mata_kuliah.'.');
    }

    public function riwayat(): View
    {
        $riwayat = Kehadiran::with('sesi.kelas')
            ->where('mahasiswa_id', auth()->id())
            ->latest('waktu_scan')
            ->paginate(15);

        return view('mahasiswa.presensi.riwayat', compact('riwayat'));
    }

    public function kelas(): View
    {
        $kelas = auth()->user()->kelasDiikuti()
            ->with('dosen')
            ->withCount('sesi')
            ->paginate(15);

        return view('mahasiswa.kelas.index', compact('kelas'));
    }
}
