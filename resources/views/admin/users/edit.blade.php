@extends('layouts.app')

@section('title', 'Edit User - Admin')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-6">
    <h1 class="text-xl font-bold mb-4">Edit User</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" required class="w-full border rounded px-3 py-2" onchange="toggleFields(this.value)">
                <option value="dosen" {{ old('role', $user->role) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="mahasiswa" {{ old('role', $user->role) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
            </select>
            @error('role')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Username (NIM/NIP)</label>
            <input name="username" value="{{ old('username', $user->username) }}" required maxlength="32" class="w-full border rounded px-3 py-2">
            @error('username')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input name="nama" value="{{ old('nama', $user->nama) }}" required maxlength="100" class="w-full border rounded px-3 py-2">
            @error('nama')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div id="field-mahasiswa">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Program Studi</label>
                    <input name="program_studi" value="{{ old('program_studi', $user->program_studi) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fakultas</label>
                    <input name="fakultas" value="{{ old('fakultas', $user->fakultas) }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>
        </div>

        <div id="field-dosen">
            <label class="block text-sm font-medium mb-1">Jabatan</label>
            <input name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password (kosongkan jika tidak diubah)</label>
            <input type="password" name="password" minlength="6" class="w-full border rounded px-3 py-2">
            @error('password')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded border">Batal</a>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </form>
</div>

<script>
function toggleFields(role) {
    document.getElementById('field-mahasiswa').style.display = role === 'mahasiswa' ? 'block' : 'none';
    document.getElementById('field-dosen').style.display = role === 'dosen' ? 'block' : 'none';
}
toggleFields('{{ old('role', $user->role) }}');
</script>
@endsection
