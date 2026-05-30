@extends('layouts.app')

@section('title', $kelas->nama_mata_kuliah)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-500 font-mono">{{ $kelas->kode }}</p>
            <h1 class="text-2xl font-bold">{{ $kelas->nama_mata_kuliah }}</h1>
            <p class="text-sm text-slate-500">
                @if($kelas->jadwal) 🕒 {{ $kelas->jadwal }} @endif
                @if($kelas->ruang) · 🏠 {{ $kelas->ruang }} @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('dosen.sesi.create', ['kelas_id' => $kelas->id]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded text-sm">+ Sesi presensi</a>
            <a href="{{ route('dosen.kelas.edit', $kelas) }}" class="border px-3 py-2 rounded text-sm">Edit</a>
            <form method="POST" action="{{ route('dosen.kelas.destroy', $kelas) }}" onsubmit="return confirm('Yakin hapus kelas ini? Semua sesi & kehadiran ikut terhapus.')">
                @csrf @method('DELETE')
                <button class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-2 rounded text-sm">Hapus</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white shadow rounded-lg">
            <div class="p-4 border-b flex items-center justify-between">
                <h2 class="font-semibold">Mahasiswa terdaftar ({{ $kelas->mahasiswa->count() }})</h2>
            </div>
            <form method="POST" action="{{ route('dosen.kelas.enrol', $kelas) }}" class="p-4 border-b flex gap-2">
                @csrf
                <input name="nim" required placeholder="NIM (akan diambil dari Pusat Data)" class="flex-1 border rounded px-3 py-2 text-sm">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">Tambahkan</button>
            </form>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="text-left px-4 py-2">NIM</th>
                        <th class="text-left px-4 py-2">Nama</th>
                        <th class="text-left px-4 py-2">Prodi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($kelas->mahasiswa as $m)
                        <tr>
                            <td class="px-4 py-2 font-mono">{{ $m->username }}</td>
                            <td class="px-4 py-2">{{ $m->nama }}</td>
                            <td class="px-4 py-2">{{ $m->program_studi ?? '–' }}</td>
                            <td class="px-4 py-2 text-right">
                                <form method="POST" action="{{ route('dosen.kelas.unenrol', [$kelas, $m]) }}" onsubmit="return confirm('Keluarkan mahasiswa ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline text-xs">Keluarkan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-slate-500 py-6">Belum ada mahasiswa. Tambahkan dengan NIM.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="p-4 border-b">
                <h2 class="font-semibold">Sesi terbaru</h2>
            </div>
            <ul class="divide-y">
                @forelse($sesi as $s)
                    <li class="p-4">
                        <a href="{{ route('dosen.sesi.show', $s) }}" class="block hover:bg-slate-50 -m-4 p-4">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-sm">{{ $s->kode_referal }}</span>
                                @if($s->ditutup)
                                    <span class="bg-slate-200 px-2 py-0.5 rounded text-xs">Ditutup</span>
                                @elseif($s->isAktif())
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs">Aktif</span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-xs">Non-aktif</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-700 mt-1">{{ $s->topik ?? '(tanpa topik)' }}</p>
                            <p class="text-xs text-slate-500">{{ $s->mulai->format('d M Y H:i') }} – {{ $s->selesai->format('H:i') }}</p>
                        </a>
                    </li>
                @empty
                    <li class="p-6 text-center text-slate-500 text-sm">Belum ada sesi.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
