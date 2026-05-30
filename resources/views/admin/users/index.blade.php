@extends('layouts.app')

@section('title', 'Kelola User - Admin')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Kelola User</h1>
            <p class="text-sm text-slate-500">Manage dosen dan mahasiswa</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">+ Tambah User</a>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 rounded text-sm {{ !request('role') ? 'bg-indigo-600 text-white' : 'border' }}">Semua</a>
        <a href="{{ route('admin.users.index', ['role' => 'dosen']) }}" class="px-3 py-1.5 rounded text-sm {{ request('role') === 'dosen' ? 'bg-indigo-600 text-white' : 'border' }}">Dosen</a>
        <a href="{{ route('admin.users.index', ['role' => 'mahasiswa']) }}" class="px-3 py-1.5 rounded text-sm {{ request('role') === 'mahasiswa' ? 'bg-indigo-600 text-white' : 'border' }}">Mahasiswa</a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-2">Username</th>
                    <th class="text-left px-4 py-2">Nama</th>
                    <th class="text-left px-4 py-2">Role</th>
                    <th class="text-left px-4 py-2">Info</th>
                    <th class="text-left px-4 py-2">Terdaftar</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($users as $u)
                    <tr>
                        <td class="px-4 py-2 font-mono text-xs">{{ $u->username }}</td>
                        <td class="px-4 py-2">{{ $u->nama }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-0.5 rounded text-xs {{ $u->role === 'dosen' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-xs">
                            @if($u->role === 'dosen')
                                {{ $u->jabatan ?? '–' }}
                            @else
                                {{ $u->program_studi ?? '–' }}
                            @endif
                        </td>
                        <td class="px-4 py-2 text-xs">{{ $u->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('admin.users.edit', $u) }}" class="text-indigo-600 hover:underline text-xs mr-2">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600 hover:underline text-xs">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-slate-500 py-8">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
