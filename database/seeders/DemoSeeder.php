<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use App\Services\PusatDataClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin', 'role' => User::ROLE_ADMIN],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('admin123'),
            ],
        );
        $this->command->info("Admin demo: {$admin->nama} ({$admin->username}). Password = admin123");

        $pusatData = app(PusatDataClient::class);

        if (! $pusatData->ping()) {
            $this->command->warn('Pusat Data tidak bisa diakses. Login admin lalu konfigurasi API token.');
            $this->command->info('--- Akun admin ---');
            $this->command->info("Admin: admin / admin123");
            return;
        }

        $nipDosenDemo = '198703152012012001';
        $dosen = $this->upsertDosen($pusatData, $nipDosenDemo);
        if (! $dosen) {
            $this->command->warn("Dosen NIP {$nipDosenDemo} tidak ditemukan di Pusat Data.");
            return;
        }
        $this->command->info("Dosen demo: {$dosen->nama} ({$dosen->username}). Password = NIM/NIP.");

        $kelasDemo = [
            [
                'kode' => 'IF-WEB-A',
                'nama_mata_kuliah' => 'Pemrograman Web',
                'ruang' => 'Lab Komputer 1',
                'jadwal' => 'Senin 08:00-10:00',
            ],
            [
                'kode' => 'IF-DB-A',
                'nama_mata_kuliah' => 'Basis Data Lanjut',
                'ruang' => 'Lab Komputer 2',
                'jadwal' => 'Rabu 13:00-15:00',
            ],
        ];

        $kelasInstances = [];
        foreach ($kelasDemo as $k) {
            $kelasInstances[] = Kelas::firstOrCreate(
                ['kode' => $k['kode']],
                $k + ['dosen_id' => $dosen->id],
            );
        }

        $nimMahasiswaDemo = ['2021001', '2021002', '2021003', '2021004', '2021007'];
        foreach ($nimMahasiswaDemo as $nim) {
            $mahasiswa = $this->upsertMahasiswa($pusatData, $nim);
            if (! $mahasiswa) {
                $this->command->warn("NIM {$nim} tidak ditemukan, dilewati.");
                continue;
            }
            foreach ($kelasInstances as $kelas) {
                $kelas->mahasiswa()->syncWithoutDetaching([$mahasiswa->id]);
            }
            $this->command->info("Mahasiswa {$mahasiswa->nama} ({$nim}) didaftarkan ke ".count($kelasInstances)." kelas.");
        }

        $this->command->info('--- Akun demo ---');
        $this->command->info("Admin    : admin / admin123");
        $this->command->info("Dosen    : {$nipDosenDemo} / {$nipDosenDemo}");
        foreach ($nimMahasiswaDemo as $nim) {
            $this->command->info("Mahasiswa: {$nim} / {$nim}");
        }
    }

    protected function upsertDosen(PusatDataClient $client, string $nip): ?User
    {
        $data = $client->findDosen($nip);
        if (! $data) return null;

        return User::updateOrCreate(
            ['username' => $nip, 'role' => User::ROLE_DOSEN],
            [
                'nama' => $data['nama'] ?? $nip,
                'jabatan' => $data['jabatan'] ?? 'Dosen',
                'password' => Hash::make($nip),
                'last_synced_at' => now(),
            ],
        );
    }

    protected function upsertMahasiswa(PusatDataClient $client, string $nim): ?User
    {
        $data = $client->findMahasiswa($nim);
        if (! $data) return null;

        $unit = $data['unit'] ?? [];

        return User::updateOrCreate(
            ['username' => $nim, 'role' => User::ROLE_MAHASISWA],
            [
                'nama' => $data['nama'] ?? $nim,
                'program_studi' => $unit['nama'] ?? null,
                'fakultas' => $unit['parent']['nama'] ?? null,
                'password' => Hash::make($nim),
                'last_synced_at' => now(),
            ],
        );
    }
}
