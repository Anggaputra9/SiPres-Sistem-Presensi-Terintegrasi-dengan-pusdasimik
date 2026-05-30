@extends('layouts.app')

@section('title', 'Kelas - Dosen')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Kelas yang Anda ampu</h1>
    <a href="{{ route('dosen.kelas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">+ Tambah kelas</a>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="text-left px-4 py-2">Kode</th>
                <th class="text-left px-4 py-2">Mata Kuliah</th>
                <th class="text-left px-4 py-2">Jadwal</th>
                <th class="text-left px-4 py-2">Ruang</th>
                <th class="text-center px-4 py-2">Mahasiswa</th>
                <th class="text-center px-4 py-2">Sesi</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($kelas as $k)
                <tr>
                    <td class="px-4 py-2 font-mono">{{ $k->kode }}</td>
                    <td class="px-4 py-2 font-medium">{{ $k->nama_mata_kuliah }}</td>
                    <td class="px-4 py-2">{{ $k->jadwal ?? '–' }}</td>
                    <td class="px-4 py-2">{{ $k->ruang ?? '–' }}</td>
                    <td class="px-4 py-2 text-center">{{ $k->mahasiswa_count }}</td>
                    <td class="px-4 py-2 text-center">{{ $k->sesi_count }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('dosen.kelas.show', $k) }}" class="text-indigo-600 hover:underline">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-slate-500 py-6">Belum ada kelas. Klik "+ Tambah kelas".</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $kelas->links() }}</div>
@endsection
