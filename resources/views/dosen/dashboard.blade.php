@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Selamat datang, {{ auth()->user()->nama }}</h1>
        <p class="text-sm text-slate-500">NIP: {{ auth()->user()->username }} · {{ auth()->user()->jabatan ?? 'Dosen' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Kelas yang diampu</p>
            <p class="text-3xl font-bold mt-2">{{ $jumlahKelas }}</p>
            <a href="{{ route('dosen.kelas.index') }}" class="text-indigo-600 text-sm hover:underline">Lihat kelas →</a>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Sesi presensi aktif</p>
            <p class="text-3xl font-bold mt-2 text-emerald-600">{{ $jumlahSesiAktif }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Kehadiran tercatat hari ini</p>
            <p class="text-3xl font-bold mt-2 text-indigo-600">{{ $totalKehadiranHariIni }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-5 border-b flex items-center justify-between">
            <h2 class="font-semibold">Sesi presensi terbaru</h2>
            <a href="{{ route('dosen.sesi.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded text-sm">+ Buat sesi</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="text-left px-4 py-2">Kode</th>
                        <th class="text-left px-4 py-2">Kelas</th>
                        <th class="text-left px-4 py-2">Topik</th>
                        <th class="text-left px-4 py-2">Mulai</th>
                        <th class="text-left px-4 py-2">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($sesiTerbaru as $s)
                        <tr>
                            <td class="px-4 py-2 font-mono">{{ $s->kode_referal }}</td>
                            <td class="px-4 py-2">{{ $s->kelas->kode }} – {{ $s->kelas->nama_mata_kuliah }}</td>
                            <td class="px-4 py-2">{{ $s->topik ?? '–' }}</td>
                            <td class="px-4 py-2">{{ $s->mulai->format('d M Y H:i') }}</td>
                            <td class="px-4 py-2">
                                @if($s->ditutup)
                                    <span class="bg-slate-200 px-2 py-0.5 rounded text-xs">Ditutup</span>
                                @elseif($s->isAktif())
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs">Aktif</span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-xs">Belum/Selesai</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('dosen.sesi.show', $s) }}" class="text-indigo-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-slate-500 py-6">Belum ada sesi presensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
