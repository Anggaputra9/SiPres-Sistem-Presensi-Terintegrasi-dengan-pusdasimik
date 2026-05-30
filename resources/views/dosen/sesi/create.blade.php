@extends('layouts.app')

@section('title', 'Buat Sesi Presensi')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
    <h1 class="text-xl font-bold mb-4">Buat sesi presensi</h1>

    <form method="POST" action="{{ route('dosen.sesi.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Kelas</label>
            <select name="kelas_id" required class="w-full border rounded px-3 py-2">
                <option value="">– pilih kelas –</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ old('kelas_id', $kelasIdTerpilih) == $k->id ? 'selected' : '' }}>
                        {{ $k->kode }} – {{ $k->nama_mata_kuliah }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Topik (opsional)</label>
            <input name="topik" value="{{ old('topik') }}" maxlength="150" class="w-full border rounded px-3 py-2" placeholder="contoh: Pertemuan 5 – Routing">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Mulai</label>
                <input type="datetime-local" name="mulai" value="{{ old('mulai', now()->format('Y-m-d\TH:i')) }}" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Selesai</label>
                <input type="datetime-local" name="selesai" value="{{ old('selesai', now()->addHours(2)->format('Y-m-d\TH:i')) }}" required class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('dosen.sesi.index') }}" class="px-4 py-2 rounded border">Batal</a>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Buat sesi</button>
        </div>
    </form>
</div>
@endsection
