@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Dashboard Admin</h1>
        <p class="text-sm text-slate-500">Kelola sistem presensi</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Total Dosen</p>
            <p class="text-3xl font-bold mt-2 text-indigo-600">{{ $totalDosen }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Total Mahasiswa</p>
            <p class="text-3xl font-bold mt-2 text-emerald-600">{{ $totalMahasiswa }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Total Kelas</p>
            <p class="text-3xl font-bold mt-2 text-amber-600">{{ $totalKelas }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Total Sesi Presensi</p>
            <p class="text-3xl font-bold mt-2 text-blue-600">{{ $totalSesi }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Sesi Aktif</p>
            <p class="text-3xl font-bold mt-2 text-green-600">{{ $sesiAktif }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm text-slate-500">Total Kehadiran</p>
            <p class="text-3xl font-bold mt-2 text-purple-600">{{ $totalKehadiran }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="font-semibold">User Terbaru</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="text-left px-4 py-2">Username</th>
                        <th class="text-left px-4 py-2">Nama</th>
                        <th class="text-left px-4 py-2">Role</th>
                        <th class="text-left px-4 py-2">Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentUsers as $u)
                        <tr>
                            <td class="px-4 py-2 font-mono text-xs">{{ $u->username }}</td>
                            <td class="px-4 py-2">{{ $u->nama }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded text-xs {{ $u->role === 'dosen' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-xs">{{ $u->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-slate-500 py-6">Belum ada user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="font-semibold">Sesi Presensi Terbaru</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="text-left px-4 py-2">Kode</th>
                        <th class="text-left px-4 py-2">Kelas</th>
                        <th class="text-left px-4 py-2">Dosen</th>
                        <th class="text-left px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentSesi as $s)
                        <tr>
                            <td class="px-4 py-2 font-mono text-xs">{{ $s->kode_referal }}</td>
                            <td class="px-4 py-2">{{ $s->kelas->kode }}</td>
                            <td class="px-4 py-2 text-xs">{{ $s->dosen->nama }}</td>
                            <td class="px-4 py-2">
                                @if($s->ditutup)
                                    <span class="bg-slate-200 px-2 py-0.5 rounded text-xs">Ditutup</span>
                                @elseif($s->isAktif())
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs">Aktif</span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded text-xs">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-slate-500 py-6">Belum ada sesi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
