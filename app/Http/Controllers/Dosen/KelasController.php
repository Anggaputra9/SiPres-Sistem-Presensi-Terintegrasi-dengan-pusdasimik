<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Services\PusatDataClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function __construct(protected PusatDataClient $pusatData) {}

    public function index(): View
    {
        $kelas = Kelas::where('dosen_id', auth()->id())
            ->withCount('mahasiswa', 'sesi')
            ->latest()
            ->paginate(10);

        return view('dosen.kelas.index', compact('kelas'));
    }

    public function create(): View
    {
        return view('dosen.kelas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => 'required|string|max:20|unique:kelas,kode',
            'nama_mata_kuliah' => 'required|string|max:150',
            'ruang' => 'nullable|string|max:50',
            'jadwal' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $data['dosen_id'] = auth()->id();
        $kelas = Kelas::create($data);

        return redirect()
            ->route('dosen.kelas.show', $kelas)
            ->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(Kelas $kelas): View
    {
        $this->authorizeKelas($kelas);

        $kelas->load('mahasiswa');
        $sesi = $kelas->sesi()->latest()->take(10)->get();

        return view('dosen.kelas.show', compact('kelas', 'sesi'));
    }

    public function edit(Kelas $kelas): View
    {
        $this->authorizeKelas($kelas);

        return view('dosen.kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        $data = $request->validate([
            'kode' => 'required|string|max:20|unique:kelas,kode,'.$kelas->id,
            'nama_mata_kuliah' => 'required|string|max:150',
            'ruang' => 'nullable|string|max:50',
            'jadwal' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $kelas->update($data);

        return redirect()->route('dosen.kelas.show', $kelas)
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        $kelas->delete();

        return redirect()->route('dosen.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    public function enrol(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        $data = $request->validate([
            'nim' => 'required|string|max:20',
        ]);

        $nim = trim($data['nim']);

        $mahasiswa = User::where('username', $nim)
            ->where('role', User::ROLE_MAHASISWA)
            ->first();

        if (! $mahasiswa) {
            $mhs = $this->pusatData->findMahasiswa($nim);

            if (! $mhs) {
                return back()->withErrors(['nim' => "NIM {$nim} tidak ditemukan di Pusat Data."])
                    ->withInput();
            }

            $unit = $mhs['unit'] ?? [];

            $mahasiswa = User::create([
                'username' => $nim,
                'role' => User::ROLE_MAHASISWA,
                'nama' => $mhs['nama'],
                'program_studi' => $unit['nama'] ?? null,
                'fakultas' => $unit['parent']['nama'] ?? null,
                'password' => Hash::make($nim),
                'last_synced_at' => now(),
            ]);
        }

        if ($kelas->mahasiswa()->where('mahasiswa_id', $mahasiswa->id)->exists()) {
            return back()->with('warning', "{$mahasiswa->nama} sudah terdaftar di kelas ini.");
        }

        $kelas->mahasiswa()->attach($mahasiswa->id);

        return back()->with('success', "{$mahasiswa->nama} berhasil ditambahkan ke kelas.");
    }

    public function unenrol(Kelas $kelas, User $mahasiswa): RedirectResponse
    {
        $this->authorizeKelas($kelas);

        $kelas->mahasiswa()->detach($mahasiswa->id);

        return back()->with('success', "{$mahasiswa->nama} dikeluarkan dari kelas.");
    }

    protected function authorizeKelas(Kelas $kelas): void
    {
        abort_unless($kelas->dosen_id === auth()->id(), 403);
    }
}
