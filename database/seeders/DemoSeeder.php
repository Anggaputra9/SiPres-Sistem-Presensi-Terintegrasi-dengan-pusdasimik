<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // Admin
        // =========================
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'role' => User::ROLE_ADMIN,
                'nama' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        // =========================
        // Lecturer
        // =========================
        $dosen = User::updateOrCreate(
            ['username' => 'dosen'],
            [
                'role' => User::ROLE_DOSEN,
                'nama' => 'Demo Lecturer',
                'jabatan' => 'Dosen',
                'password' => Hash::make('dosen123'),
            ]
        );

        // =========================
        // Students
        // =========================
        $students = [];

        for ($i = 1; $i <= 5; $i++) {

            $nim = '20210' . sprintf('%02d', $i);

            $students[] = User::updateOrCreate(
                ['username' => $nim],
                [
                    'role' => User::ROLE_MAHASISWA,
                    'nama' => 'Mahasiswa ' . $i,
                    'program_studi' => 'Informatika',
                    'fakultas' => 'Teknik',
                    'password' => Hash::make($nim),
                ]
            );
        }

        // =========================
        // Classes
        // =========================
        $kelas1 = Kelas::updateOrCreate(
            ['kode' => 'IF101'],
            [
                'nama_mata_kuliah' => 'Pemrograman Web',
                'ruang' => 'Lab 1',
                'jadwal' => 'Senin 08:00',
                'dosen_id' => $dosen->id,
            ]
        );

        $kelas2 = Kelas::updateOrCreate(
            ['kode' => 'IF102'],
            [
                'nama_mata_kuliah' => 'Basis Data',
                'ruang' => 'Lab 2',
                'jadwal' => 'Rabu 10:00',
                'dosen_id' => $dosen->id,
            ]
        );

        foreach ($students as $student) {

            $kelas1->mahasiswa()->syncWithoutDetaching($student->id);

            $kelas2->mahasiswa()->syncWithoutDetaching($student->id);
        }

        $this->command->info('');
        $this->command->info('===================================');
        $this->command->info(' Demo accounts created successfully ');
        $this->command->info('===================================');

        $this->command->info('Admin');
        $this->command->info('Username : admin');
        $this->command->info('Password : admin123');

        $this->command->info('');

        $this->command->info('Lecturer');
        $this->command->info('Username : dosen');
        $this->command->info('Password : dosen123');

        $this->command->info('');

        $this->command->info('Students');

        foreach ($students as $student) {

            $this->command->info(
                "{$student->username} / {$student->username}"
            );
        }
    }
}