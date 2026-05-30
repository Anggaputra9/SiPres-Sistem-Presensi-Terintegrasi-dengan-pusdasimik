@extends('layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
<h1 class="text-2xl font-bold mb-4">Riwayat presensi saya</h1>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="text-left px-4 py-2">Waktu</th>
                <th class="text-left px-4 py-2">Kelas</th>
                <th class="text-left px-4 py-2">Topik</th>
                <th class="text-left px-4 py-2">Status</th>
                <th class="text-left px-4 py-2">Catatan</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($riwayat as $r)
                @php
                    $color = match($r->status) {
                        'hadir' => 'bg-emerald-100 text-emerald-700',
                        'terlambat' => 'bg-amber-100 text-amber-700',
                        'izin' => 'bg-sky-100 text-sky-700',
                        'sakit' => 'bg-violet-100 text-violet-700',
                        'alpha' => 'bg-rose-100 text-rose-700',
                        default => 'bg-slate-100 text-slate-700',
                    };
                @endphp
                <tr>
                    <td class="px-4 py-2">{{ $r->waktu_scan->format('d M Y H:i') }}</td>
                    <td class="px-4 py-2">{{ $r->sesi->kelas->kode }} – {{ $r->sesi->kelas->nama_mata_kuliah }}</td>
                    <td class="px-4 py-2">{{ $r->sesi->topik ?? '–' }}</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs {{ $color }}">{{ $r->status_label }}</span></td>
                    <td class="px-4 py-2 text-slate-500">{{ $r->catatan ?? '–' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-slate-500 py-8">Belum ada riwayat presensi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $riwayat->links() }}</div>
@endsection
