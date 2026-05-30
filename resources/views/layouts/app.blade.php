<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Presensi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head')
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">
    @auth
    <nav class="bg-indigo-700 text-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isDosen() ? route('dosen.dashboard') : route('mahasiswa.dashboard')) }}" class="font-bold text-lg">
                    📚 Sistem Presensi
                </a>
                @if(auth()->user()->isAdmin())
                    <div class="hidden md:flex gap-4 text-sm">
                        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
                        <a href="{{ route('admin.settings.index') }}" class="hover:underline">Konfigurasi API</a>
                        <a href="{{ route('admin.users.index') }}" class="hover:underline">Kelola User</a>
                    </div>
                @elseif(auth()->user()->isDosen())
                    <div class="hidden md:flex gap-4 text-sm">
                        <a href="{{ route('dosen.dashboard') }}" class="hover:underline">Dashboard</a>
                        <a href="{{ route('dosen.kelas.index') }}" class="hover:underline">Kelas</a>
                        <a href="{{ route('dosen.sesi.index') }}" class="hover:underline">Sesi Presensi</a>
                    </div>
                @else
                    <div class="hidden md:flex gap-4 text-sm">
                        <a href="{{ route('mahasiswa.dashboard') }}" class="hover:underline">Dashboard</a>
                        <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:underline">Kelas Saya</a>
                        <a href="{{ route('mahasiswa.presensi.form') }}" class="hover:underline">Scan Presensi</a>
                        <a href="{{ route('mahasiswa.presensi.riwayat') }}" class="hover:underline">Riwayat</a>
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="hidden sm:inline">{{ auth()->user()->nama }} <span class="opacity-75">({{ ucfirst(auth()->user()->role) }})</span></span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="bg-indigo-900 hover:bg-indigo-950 px-3 py-1.5 rounded">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <main class="flex-1">
        <div class="max-w-6xl mx-auto px-4 py-6">
            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-2 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-amber-100 border border-amber-300 text-amber-800 px-4 py-2 rounded mb-4">
                    {{ session('warning') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-100 border border-rose-300 text-rose-800 px-4 py-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-rose-100 border border-rose-300 text-rose-800 px-4 py-2 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t text-xs text-slate-500 text-center py-3">
        Sistem Presensi · terintegrasi Pusat Data
    </footer>

    @stack('scripts')
</body>
</html>
