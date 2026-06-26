<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Kehadiran;
use App\Models\SesiPresensi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KelasPresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Dosen
        $dosen1 = User::firstOrCreate(
            ['username' => '0012345678'],
            [
                'nama' => 'Dr. Ahmad Santoso, M.Kom',
                'role' => User::ROLE_DOSEN,
                'password' => Hash::make('0012345678'),
                'jabatan' => 'Dosen',
            ]
        );

        $dosen2 = User::firstOrCreate(
            ['username' => '0087654321'],
            [
                'nama' => 'Dr. Siti Nurhaliza, M.T',
                'role' => User::ROLE_DOSEN,
                'password' => Hash::make('0087654321'),
                'jabatan' => 'Dosen',
            ]
        );

        // 2. Buat Mahasiswa dengan berbagai skenario
        $mahasiswaData = [
            // Mahasiswa dengan kehadiran >= 75% (ELIGIBLE)
            ['username' => '2024001', 'nama' => 'Budi Santoso', 'prodi' => 'Teknik Informatika'],
            ['username' => '2024002', 'nama' => 'Ani Wijaya', 'prodi' => 'Sistem Informasi'],
            ['username' => '2024003', 'nama' => 'Citra Dewi', 'prodi' => 'Teknik Informatika'],
            
            // Mahasiswa dengan kehadiran < 75% (NOT ELIGIBLE)
            ['username' => '2024004', 'nama' => 'Dedi Kurniawan', 'prodi' => 'Teknik Informatika'],
            ['username' => '2024005', 'nama' => 'Eka Putri', 'prodi' => 'Sistem Informasi'],
        ];

        $mahasiswaList = [];
        foreach ($mahasiswaData as $data) {
            $mahasiswaList[] = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'nama' => $data['nama'],
                    'role' => User::ROLE_MAHASISWA,
                    'password' => Hash::make($data['username']),
                    'program_studi' => $data['prodi'],
                    'fakultas' => 'Fakultas Teknik',
                ]
            );
        }

        // 3. Buat Kelas
        $kelas1 = Kelas::firstOrCreate(
            ['kode' => 'TIF301'],
            [
                'nama_mata_kuliah' => 'Pemrograman Web Lanjut',
                'dosen_id' => $dosen1->id,
                'ruang' => 'Lab Komputer 1',
                'jadwal' => 'Senin 08:00-10:00',
                'deskripsi' => 'Mata kuliah pemrograman web tingkat lanjut - Genap 2025/2026',
            ]
        );

        $kelas2 = Kelas::firstOrCreate(
            ['kode' => 'TIF302'],
            [
                'nama_mata_kuliah' => 'Basis Data Terdistribusi',
                'dosen_id' => $dosen2->id,
                'ruang' => 'Ruang 203',
                'jadwal' => 'Rabu 10:00-12:00',
                'deskripsi' => 'Mata kuliah basis data terdistribusi - Genap 2025/2026',
            ]
        );

        // 4. Daftarkan mahasiswa ke kelas (pivot table)
        foreach ($mahasiswaList as $mhs) {
            $kelas1->mahasiswa()->syncWithoutDetaching([$mhs->id]);
            $kelas2->mahasiswa()->syncWithoutDetaching([$mhs->id]);
        }

        // 5. Buat 20 Sesi Presensi untuk setiap kelas
        $tanggalMulai = now()->subMonths(3);
        
        for ($i = 0; $i < 20; $i++) {
            $tanggal = $tanggalMulai->copy()->addDays($i * 3); // Setiap 3 hari

            // Sesi untuk Kelas 1
            $sesi1 = SesiPresensi::create([
                'kelas_id' => $kelas1->id,
                'dosen_id' => $dosen1->id,
                'topik' => 'Pertemuan ' . ($i + 1) . ' - ' . $kelas1->nama_mata_kuliah,
                'mulai' => $tanggal->copy()->setTime(8, 0),
                'selesai' => $tanggal->copy()->setTime(10, 30),
                'kode_referal' => SesiPresensi::generateKodeReferal(),
            ]);

            // Sesi untuk Kelas 2
            $sesi2 = SesiPresensi::create([
                'kelas_id' => $kelas2->id,
                'dosen_id' => $dosen2->id,
                'topik' => 'Pertemuan ' . ($i + 1) . ' - ' . $kelas2->nama_mata_kuliah,
                'mulai' => $tanggal->copy()->setTime(13, 0),
                'selesai' => $tanggal->copy()->setTime(15, 30),
                'kode_referal' => SesiPresensi::generateKodeReferal(),
            ]);

            // 6. Buat Kehadiran untuk setiap mahasiswa
            // Mahasiswa 2024001, 2024002, 2024003: hadir >= 75%
            // Mahasiswa 2024004, 2024005: hadir < 75%
            
            foreach ($mahasiswaList as $index => $mhs) {
                // Tentukan tingkat kehadiran berdasarkan mahasiswa
                $shouldAttend = false;
                
                if (in_array($mhs->username, ['2024001', '2024002', '2024003'])) {
                    // 80-90% hadir (eligible)
                    $shouldAttend = rand(1, 100) <= 85;
                } else {
                    // 50-65% hadir (not eligible)
                    $shouldAttend = rand(1, 100) <= 60;
                }

                if ($shouldAttend) {
                    // Kehadiran di Kelas 1
                    Kehadiran::create([
                        'sesi_presensi_id' => $sesi1->id,
                        'mahasiswa_id' => $mhs->id,
                        'status' => rand(1, 100) <= 90 ? 'hadir' : 'terlambat',
                        'waktu_scan' => $tanggal->copy()->setTime(8, rand(0, 30)),
                    ]);

                    // Kehadiran di Kelas 2
                    Kehadiran::create([
                        'sesi_presensi_id' => $sesi2->id,
                        'mahasiswa_id' => $mhs->id,
                        'status' => rand(1, 100) <= 90 ? 'hadir' : 'terlambat',
                        'waktu_scan' => $tanggal->copy()->setTime(13, rand(0, 30)),
                    ]);
                }
            }
        }

        $this->command->info('✅ Seeder Kelas dan Presensi berhasil!');
        $this->command->info('📊 Data yang dibuat:');
        $this->command->info('   - 2 Dosen');
        $this->command->info('   - 5 Mahasiswa (3 eligible, 2 not eligible)');
        $this->command->info('   - 2 Kelas');
        $this->command->info('   - 40 Sesi Presensi (20 per kelas)');
        $this->command->info('   - ~240 Record Kehadiran');
    }
}
