@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->nama }}</h1>
        <p class="text-sm text-slate-500">
            NIM: {{ auth()->user()->username }}
            @if(auth()->user()->program_studi) · {{ auth()->user()->program_studi }} @endif
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Kelas yang diikuti</p>
            <p class="text-3xl font-bold mt-2">{{ $jumlahKelas }}</p>
            <a href="{{ route('mahasiswa.kelas.index') }}" class="text-indigo-600 text-sm hover:underline">Lihat kelas →</a>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Total kehadiran</p>
            <p class="text-3xl font-bold mt-2 text-emerald-600">{{ $totalHadir }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Total alpha</p>
            <p class="text-3xl font-bold mt-2 text-rose-600">{{ $totalAlpha }}</p>
        </div>
    </div>

    <div class="bg-indigo-600 text-white rounded-lg shadow p-6 flex items-center justify-between">
        <div>
            <p class="text-lg font-semibold">Mau presensi sekarang?</p>
            <p class="text-sm opacity-90">Scan QR dari dosen atau ketik kode referalnya.</p>
        </div>
        <a href="{{ route('mahasiswa.presensi.form') }}" class="bg-white text-indigo-700 font-semibold px-4 py-2 rounded hover:bg-indigo-50">📷 Scan presensi</a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <h2 class="font-semibold">Riwayat presensi terbaru</h2>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-2">Tanggal</th>
                    <th class="text-left px-4 py-2">Kelas</th>
                    <th class="text-left px-4 py-2">Topik</th>
                    <th class="text-left px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($riwayat as $r)
                    <tr>
                        <td class="px-4 py-2">{{ $r->waktu_scan->format('d M Y H:i') }}</td>
                        <td class="px-4 py-2">{{ $r->sesi->kelas->kode }} – {{ $r->sesi->kelas->nama_mata_kuliah }}</td>
                        <td class="px-4 py-2">{{ $r->sesi->topik ?? '–' }}</td>
                        <td class="px-4 py-2">{{ $r->status_label }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-slate-500 py-6">Belum ada riwayat presensi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
