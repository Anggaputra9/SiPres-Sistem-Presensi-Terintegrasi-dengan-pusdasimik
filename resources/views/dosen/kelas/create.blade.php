@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
    <h1 class="text-xl font-bold mb-4">Tambah Kelas</h1>

    <form method="POST" action="{{ route('dosen.kelas.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Kode kelas</label>
            <input name="kode" value="{{ old('kode') }}" required maxlength="20"
                class="w-full border rounded px-3 py-2" placeholder="contoh: IF-2024-A">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nama mata kuliah</label>
            <input name="nama_mata_kuliah" value="{{ old('nama_mata_kuliah') }}" required maxlength="150"
                class="w-full border rounded px-3 py-2" placeholder="contoh: Pemrograman Web">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Ruang</label>
                <input name="ruang" value="{{ old('ruang') }}" maxlength="50" class="w-full border rounded px-3 py-2" placeholder="Lab A">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jadwal</label>
                <input name="jadwal" value="{{ old('jadwal') }}" maxlength="100" class="w-full border rounded px-3 py-2" placeholder="Senin 08:00-10:00">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full border rounded px-3 py-2">{{ old('deskripsi') }}</textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('dosen.kelas.index') }}" class="px-4 py-2 rounded border">Batal</a>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
