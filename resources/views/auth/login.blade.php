@extends('layouts.app')

@section('title', 'Login - Sistem Presensi')

@section('content')
<div class="max-w-md mx-auto mt-12 bg-white shadow rounded-lg p-8">
    <h1 class="text-2xl font-bold text-center mb-1">📚 Sistem Presensi</h1>
    <p class="text-center text-sm text-slate-500 mb-6">Masukkan NIM, NIP, atau username admin</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium mb-1">NIM / NIP / Username</label>
            <input id="username" name="username" type="text" required autofocus
                value="{{ old('username') }}"
                class="w-full border border-slate-300 rounded px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password</label>
            <input id="password" name="password" type="password" required
                class="w-full border border-slate-300 rounded px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <p class="text-xs text-slate-500 mt-1">Default password = NIM/NIP Anda (silakan ganti setelah login).</p>
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember"> Ingat saya
        </label>

        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-medium">
            Masuk
        </button>
    </form>
</div>
@endsection
