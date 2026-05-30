@extends('layouts.app')

@section('title', 'Kelas Saya')

@section('content')
<h1 class="text-2xl font-bold mb-4">Kelas yang saya ikuti</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($kelas as $k)
        <div class="bg-white shadow rounded-lg p-5">
            <p class="text-xs font-mono text-slate-500">{{ $k->kode }}</p>
            <h2 class="font-bold text-lg">{{ $k->nama_mata_kuliah }}</h2>
            <p class="text-sm text-slate-600">Dosen: {{ $k->dosen->nama }}</p>
            @if($k->jadwal)<p class="text-xs text-slate-500 mt-1">🕒 {{ $k->jadwal }}</p>@endif
            @if($k->ruang)<p class="text-xs text-slate-500">🏠 {{ $k->ruang }}</p>@endif
            <p class="text-xs text-slate-500 mt-2">{{ $k->sesi_count }} sesi presensi</p>
        </div>
    @empty
        <div class="col-span-full text-center text-slate-500 py-8">
            Anda belum terdaftar di kelas manapun. Hubungi dosen untuk didaftarkan.
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $kelas->links() }}</div>
@endsection
