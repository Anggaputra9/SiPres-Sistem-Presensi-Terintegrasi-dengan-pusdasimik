<?php

namespace App\Console\Commands;

use App\Models\Kehadiran;
use App\Services\PusatDataClient;
use Illuminate\Console\Command;

class SyncPresensiToPusatData extends Command
{
    protected $signature = 'presensi:sync-to-pusat';
    protected $description = 'Sync semua data kehadiran yang ada ke Pusat Data';

    public function handle(PusatDataClient $client): int
    {
        $this->info('🔄 Memulai sinkronisasi data presensi ke Pusat Data...');
        
        // Ambil semua data kehadiran dengan relasi yang dibutuhkan
        $kehadiran = Kehadiran::with(['sesi.kelas', 'mahasiswa'])
            ->orderBy('waktu_scan')
            ->get();

        if ($kehadiran->isEmpty()) {
            $this->warn('⚠️  Tidak ada data kehadiran untuk disinkronkan.');
            return Command::SUCCESS;
        }

        $this->info("📊 Total data kehadiran: {$kehadiran->count()}");
        
        $berhasil = 0;
        $gagal = 0;

        $progressBar = $this->output->createProgressBar($kehadiran->count());
        $progressBar->start();

        foreach ($kehadiran as $item) {
            try {
                $sukses = $client->kirimDataPresensi([
                    'nim_mahasiswa' => $item->mahasiswa->username,
                    'kode_kelas' => $item->sesi->kelas->kode,
                    'nama_mata_kuliah' => $item->sesi->kelas->nama_mata_kuliah,
                    'status_kehadiran' => $item->status,
                    'waktu' => $item->waktu_scan->toDateTimeString(),
                ]);

                if ($sukses) {
                    $berhasil++;
                } else {
                    $gagal++;
                    $this->newLine();
                    $this->error("❌ Gagal kirim: {$item->mahasiswa->username} - {$item->sesi->kelas->kode}");
                }
            } catch (\Exception $e) {
                $gagal++;
                $this->newLine();
                $this->error("❌ Error: {$e->getMessage()}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Sinkronisasi selesai!");
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Berhasil', $berhasil],
                ['Gagal', $gagal],
                ['Total', $kehadiran->count()],
            ]
        );

        return Command::SUCCESS;
    }
}
