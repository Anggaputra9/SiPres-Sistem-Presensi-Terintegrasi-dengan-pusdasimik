<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SesiPresensi extends Model
{
    protected $table = 'sesi_presensi';

    protected $fillable = [
        'kelas_id',
        'dosen_id',
        'kode_referal',
        'topik',
        'mulai',
        'selesai',
        'ditutup',
    ];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
        'ditutup' => 'boolean',
    ];

    public static function generateKodeReferal(): string
    {
        do {
            $kode = strtoupper(Str::random(8));
        } while (self::where('kode_referal', $kode)->exists());

        return $kode;
    }

    public function isAktif(): bool
    {
        $now = now();

        return ! $this->ditutup
            && $now->between($this->mulai, $this->selesai);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }
}
