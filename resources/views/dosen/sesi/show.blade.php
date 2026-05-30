@extends('layouts.app')

@section('title', 'Sesi '.$sesi->kode_referal)

@push('head')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white shadow rounded-lg p-6 text-center">
            <p class="text-sm text-slate-500">{{ $sesi->kelas->kode }} – {{ $sesi->kelas->nama_mata_kuliah }}</p>
            <h1 class="text-lg font-bold">{{ $sesi->topik ?? 'Sesi presensi' }}</h1>
            <p class="text-xs text-slate-500 mb-4">{{ $sesi->mulai->format('d M Y H:i') }} – {{ $sesi->selesai->format('H:i') }}</p>

            <div id="qrcode" class="flex justify-center mb-3"></div>

            <p class="text-xs text-slate-500">Kode referal:</p>
            <p class="text-3xl font-mono font-bold tracking-widest text-indigo-700">{{ $sesi->kode_referal }}</p>

            <div class="mt-4">
                @if($sesi->ditutup)
                    <span class="inline-block bg-slate-200 px-3 py-1 rounded text-sm">Ditutup</span>
                @elseif($sesi->isAktif())
                    <span class="inline-block bg-emerald-100 text-emerald-700 px-3 py-1 rounded text-sm">Aktif menerima scan</span>
                @else
                    <span class="inline-block bg-amber-100 text-amber-700 px-3 py-1 rounded text-sm">Di luar jendela waktu</span>
                @endif
            </div>

            <div class="flex gap-2 justify-center mt-4">
                @if($sesi->ditutup)
                    <form method="POST" action="{{ route('dosen.sesi.buka', $sesi) }}">
                        @csrf
                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-sm">Buka kembali</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('dosen.sesi.tutup', $sesi) }}">
                        @csrf
                        <button class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded text-sm">Tutup sesi</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('dosen.sesi.destroy', $sesi) }}" onsubmit="return confirm('Hapus sesi & semua kehadirannya?')">
                    @csrf @method('DELETE')
                    <button class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded text-sm">Hapus</button>
                </form>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 text-sm">
            <p class="font-semibold mb-2">Cara mahasiswa presensi:</p>
            <ol class="list-decimal pl-5 space-y-1 text-slate-600">
                <li>Buka menu <em>Scan Presensi</em></li>
                <li>Scan QR di atas atau ketik kode <span class="font-mono">{{ $sesi->kode_referal }}</span></li>
                <li>Sistem akan menandai status <em>hadir</em> / <em>terlambat</em> otomatis (15 menit)</li>
            </ol>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white shadow rounded-lg">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="font-semibold">Rekap kehadiran</h2>
            <span class="text-sm text-slate-500">
                {{ $sudahHadir->count() }} / {{ $mahasiswa->count() }} hadir
            </span>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-2">NIM</th>
                    <th class="text-left px-4 py-2">Nama</th>
                    <th class="text-left px-4 py-2">Status</th>
                    <th class="text-left px-4 py-2">Waktu scan</th>
                    <th class="text-left px-4 py-2">Tandai manual</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($mahasiswa as $m)
                    @php $k = $sudahHadir->get($m->id); @endphp
                    <tr>
                        <td class="px-4 py-2 font-mono">{{ $m->username }}</td>
                        <td class="px-4 py-2">{{ $m->nama }}</td>
                        <td class="px-4 py-2">
                            @if($k)
                                @php
                                    $color = match($k->status) {
                                        'hadir' => 'bg-emerald-100 text-emerald-700',
                                        'terlambat' => 'bg-amber-100 text-amber-700',
                                        'izin' => 'bg-sky-100 text-sky-700',
                                        'sakit' => 'bg-violet-100 text-violet-700',
                                        'alpha' => 'bg-rose-100 text-rose-700',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-xs {{ $color }}">{{ $k->status_label }}</span>
                            @else
                                <span class="text-slate-400 text-xs">– belum –</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-xs">
                            {{ $k?->waktu_scan?->format('d M H:i') ?? '–' }}
                        </td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('dosen.sesi.manual', $sesi) }}" class="flex gap-1">
                                @csrf
                                <input type="hidden" name="mahasiswa_id" value="{{ $m->id }}">
                                <select name="status" class="border rounded px-2 py-1 text-xs">
                                    <option value="hadir" {{ ($k?->status) === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="terlambat" {{ ($k?->status) === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                    <option value="izin" {{ ($k?->status) === 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ ($k?->status) === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="alpha" {{ ($k?->status) === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                </select>
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1 rounded text-xs">Set</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-slate-500 py-6">Kelas belum punya mahasiswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    new QRCode(document.getElementById('qrcode'), {
        text: @json($sesi->kode_referal),
        width: 220,
        height: 220,
        correctLevel: QRCode.CorrectLevel.H,
    });
</script>
@endpush
@endsection
