<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
            $table->string('kode_referal', 12)->unique()->comment('kode acak yang di-encode jadi QR');
            $table->string('topik', 150)->nullable();
            $table->dateTime('mulai');
            $table->dateTime('selesai');
            $table->boolean('ditutup')->default(false);
            $table->timestamps();

            $table->index('kelas_id');
            $table->index('kode_referal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_presensi');
    }
};
