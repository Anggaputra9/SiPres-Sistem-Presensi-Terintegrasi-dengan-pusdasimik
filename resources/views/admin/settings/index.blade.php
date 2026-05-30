@extends('layouts.app')

@section('title', 'Konfigurasi API - Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Konfigurasi API Pusat Data</h1>
        <p class="text-sm text-slate-500">Kelola koneksi ke sistem pusat data</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">URL API Pusat Data</label>
                <input name="pusat_data_api_url" value="{{ old('pusat_data_api_url', $apiUrl) }}" required
                    class="w-full border rounded px-3 py-2" placeholder="http://localhost:8000/api">
                @error('pusat_data_api_url')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">API Token</label>
                <textarea name="pusat_data_api_token" rows="3" required
                    class="w-full border rounded px-3 py-2 font-mono text-sm" placeholder="Paste token dari Pusat Data di sini">{{ old('pusat_data_api_token', $apiToken) }}</textarea>
                @error('pusat_data_api_token')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-500 mt-1">Token didapat dari admin Pusat Data. Simpan dengan aman.</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Timeout (detik)</label>
                <input type="number" name="pusat_data_api_timeout" value="{{ old('pusat_data_api_timeout', $apiTimeout) }}" min="5" max="60"
                    class="w-full border rounded px-3 py-2">
                @error('pusat_data_api_timeout')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                    Simpan Konfigurasi
                </button>
                <button type="button" onclick="testConnection()" class="border px-4 py-2 rounded hover:bg-slate-50">
                    Test Koneksi
                </button>
            </div>
        </form>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
        <p class="font-semibold text-amber-900 mb-2">Cara mendapatkan API Token:</p>
        <ol class="list-decimal pl-5 space-y-1 text-sm text-amber-800">
            <li>Login ke sistem Pusat Data sebagai admin</li>
            <li>Buka menu "API Clients" atau "Manajemen Token"</li>
            <li>Cari client "Website Presensi" atau buat baru</li>
            <li>Generate token baru dan copy ke form di atas</li>
            <li>Simpan konfigurasi dan test koneksi</li>
        </ol>
    </div>
</div>

<form id="test-form" method="POST" action="{{ route('admin.settings.test') }}" style="display:none">
    @csrf
</form>

<script>
function testConnection() {
    if (confirm('Test koneksi ke Pusat Data?')) {
        document.getElementById('test-form').submit();
    }
}
</script>
@endsection
