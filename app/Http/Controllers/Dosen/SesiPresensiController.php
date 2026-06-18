<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\SesiPresensi;
use App\Models\User;
use App\Services\PusatDataClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SesiPresensiController extends Controller
{
    public function index(): View
    {
        $sesi = SesiPresensi::with('kelas')
            ->where('dosen_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('dosen.sesi.index', compact('sesi'));
    }

    public function create(Request $request): View
    {
        $kelas = Kelas::where('dosen_id', auth()->id())->orderBy('kode')->get();
        $kelasIdTerpilih = $request->integer('kelas_id');

        return view('dosen.sesi.create', compact('kelas', 'kelasIdTerpilih'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'topik' => 'nullable|string|max:150',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after:mulai',
        ]);

        $kelas = Kelas::findOrFail($data['kelas_id']);
        abort_unless($kelas->dosen_id === auth()->id(), 403);

        $sesi = SesiPresensi::create([
            'kelas_id' => $kelas->id,
            'dosen_id' => auth()->id(),
            'kode_referal' => SesiPresensi::generateKodeReferal(),
            'topik' => $data['topik'] ?? null,
            'mulai' => $data['mulai'],
            'selesai' => $data['selesai'],
        ]);

        return redirect()->route('dosen.sesi.show', $sesi)
            ->with('success', 'Sesi presensi dibuat. Tampilkan QR ke mahasiswa.');
    }

    public function show(SesiPresensi $sesi): View
    {
        abort_unless($sesi->dosen_id === auth()->id(), 403);

        $sesi->load(['kelas.mahasiswa', 'kehadiran.mahasiswa']);

        $sudahHadir = $sesi->kehadiran->keyBy('mahasiswa_id');
        $mahasiswa = $sesi->kelas->mahasiswa;

        return view('dosen.sesi.show', compact('sesi', 'mahasiswa', 'sudahHadir'));
    }

    public function tutup(SesiPresensi $sesi): RedirectResponse
    {
        abort_unless($sesi->dosen_id === auth()->id(), 403);

        $sesi->update(['ditutup' => true]);

        return back()->with('success', 'Sesi ditutup. Tidak menerima scan baru.');
    }

    public function bukaUlang(SesiPresensi $sesi): RedirectResponse
    {
        abort_unless($sesi->dosen_id === auth()->id(), 403);

        $sesi->update(['ditutup' => false]);

        return back()->with('success', 'Sesi dibuka kembali.');
    }

    public function destroy(SesiPresensi $sesi): RedirectResponse
    {
        abort_unless($sesi->dosen_id === auth()->id(), 403);

        $sesi->delete();

        return redirect()->route('dosen.sesi.index')
            ->with('success', 'Sesi presensi dihapus.');
    }

    public function tandaiManual(Request $request, SesiPresensi $sesi, PusatDataClient $client): RedirectResponse
    {
        abort_unless($sesi->dosen_id === auth()->id(), 403);

        $data = $request->validate([
            'mahasiswa_id' => 'required|exists:users,id',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpha',
            'catatan' => 'nullable|string|max:255',
        ]);

        $waktuScan = now();

        Kehadiran::updateOrCreate(
            ['sesi_presensi_id' => $sesi->id, 'mahasiswa_id' => $data['mahasiswa_id']],
            [
                'status' => $data['status'],
                'waktu_scan' => $waktuScan,
                'catatan' => $data['catatan'] ?? null,
            ],
        );

        // Kirim data ke Pusat Data
        $mahasiswa = User::find($data['mahasiswa_id']);
        $client->kirimDataPresensi([
            'nim_mahasiswa' => $mahasiswa->username,
            'kode_kelas' => $sesi->kelas->kode,
            'nama_mata_kuliah' => $sesi->kelas->nama_mata_kuliah,
            'status_kehadiran' => $data['status'],
            'waktu' => $waktuScan->toDateTimeString(),
        ]);

        return back()->with('success', 'Status kehadiran diperbarui.');
    }
}
