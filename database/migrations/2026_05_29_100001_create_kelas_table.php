<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
            $table->string('kode', 20)->unique();
            $table->string('nama_mata_kuliah', 150);
            $table->string('ruang', 50)->nullable();
            $table->string('jadwal', 100)->nullable()->comment('contoh: Senin 08:00-10:00');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->index('dosen_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
