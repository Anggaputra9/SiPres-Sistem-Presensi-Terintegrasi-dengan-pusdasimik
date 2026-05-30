@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
    <h1 class="text-xl font-bold mb-4">Edit Kelas</h1>

    <form method="POST" action="{{ route('dosen.kelas.update', $kelas) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Kode kelas</label>
            <input name="kode" value="{{ old('kode', $kelas->kode) }}" required maxlength="20" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nama mata kuliah</label>
            <input name="nama_mata_kuliah" value="{{ old('nama_mata_kuliah', $kelas->nama_mata_kuliah) }}" required maxlength="150" class="w-full border rounded px-3 py-2">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Ruang</label>
                <input name="ruang" value="{{ old('ruang', $kelas->ruang) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jadwal</label>
                <input name="jadwal" value="{{ old('jadwal', $kelas->jadwal) }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full border rounded px-3 py-2">{{ old('deskripsi', $kelas->deskripsi) }}</textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('dosen.kelas.show', $kelas) }}" class="px-4 py-2 rounded border">Batal</a>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
