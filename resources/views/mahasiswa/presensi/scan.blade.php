@extends('layouts.app')

@section('title', 'Scan Presensi')

@push('head')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endpush

@section('content')
<div class="max-w-xl mx-auto space-y-4">
    <h1 class="text-2xl font-bold">Scan Presensi</h1>
    <p class="text-sm text-slate-600">Arahkan kamera ke QR yang ditampilkan dosen, atau ketik kode referal manual.</p>

    <div class="bg-white shadow rounded-lg p-4">
        <div id="reader" class="rounded overflow-hidden"></div>
        <div class="flex gap-2 mt-3">
            <button id="btn-start" type="button" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded">📷 Mulai kamera</button>
            <button id="btn-stop" type="button" class="flex-1 bg-slate-300 hover:bg-slate-400 py-2 rounded hidden">Stop</button>
        </div>
        <p id="scan-status" class="text-xs text-slate-500 mt-2"></p>
    </div>

    <div class="bg-white shadow rounded-lg p-4">
        <p class="text-sm font-medium mb-2">Atau ketik kode manual:</p>
        <form method="POST" action="{{ route('mahasiswa.presensi.submit') }}" class="flex gap-2">
            @csrf
            <input id="kode-input" name="kode_referal" required maxlength="32"
                value="{{ old('kode_referal') }}"
                placeholder="contoh: A7B3X9KM"
                class="flex-1 border rounded px-3 py-2 font-mono uppercase tracking-widest">
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">Kirim</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const html5QrCode = new Html5Qrcode("reader");
    const startBtn = document.getElementById('btn-start');
    const stopBtn = document.getElementById('btn-stop');
    const status = document.getElementById('scan-status');
    const kodeInput = document.getElementById('kode-input');

    let scanning = false;

    function onSuccess(decoded) {
        if (!scanning) return;
        scanning = false;
        status.textContent = 'Kode terbaca: ' + decoded + ' — mengirim...';
        kodeInput.value = decoded;
        html5QrCode.stop().finally(() => {
            kodeInput.form.submit();
        });
    }

    startBtn.addEventListener('click', async () => {
        try {
            await html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: 240 },
                onSuccess,
            );
            scanning = true;
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            status.textContent = 'Kamera aktif — arahkan ke QR.';
        } catch (e) {
            status.textContent = 'Tidak bisa membuka kamera: ' + e;
        }
    });

    stopBtn.addEventListener('click', async () => {
        try { await html5QrCode.stop(); } catch (e) {}
        scanning = false;
        startBtn.classList.remove('hidden');
        stopBtn.classList.add('hidden');
        status.textContent = 'Kamera dimatikan.';
    });
</script>
@endpush
@endsection
