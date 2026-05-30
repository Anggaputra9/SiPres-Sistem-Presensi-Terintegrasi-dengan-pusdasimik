@extends('layouts.app')

@section('title', 'Sesi Presensi')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Sesi Presensi</h1>
    <a href="{{ route('dosen.sesi.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">+ Buat sesi</a>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="text-left px-4 py-2">Kode</th>
                <th class="text-left px-4 py-2">Kelas</th>
                <th class="text-left px-4 py-2">Topik</th>
                <th class="text-left px-4 py-2">Waktu</th>
                <th class="text-left px-4 py-2">Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($sesi as $s)
                <tr>
                    <td class="px-4 py-2 font-mono">{{ $s->kode_referal }}</td>
                    <td class="px-4 py-2">{{ $s->kelas->kode }}</td>
                    <td class="px-4 py-2">{{ $s->topik ?? '–' }}</td>
                    <td class="px-4 py-2">{{ $s->mulai->format('d M Y H:i') }} – {{ $s->selesai->format('H:i') }}</td>
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

<div class="mt-4">{{ $sesi->links() }}</div>
@endsection
