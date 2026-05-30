<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kehadiran extends Model
{
    public const STATUS_HADIR = 'hadir';
    public const STATUS_TERLAMBAT = 'terlambat';
    public const STATUS_IZIN = 'izin';
    public const STATUS_SAKIT = 'sakit';
    public const STATUS_ALPHA = 'alpha';

    public const STATUS_LABELS = [
        self::STATUS_HADIR => 'Hadir',
        self::STATUS_TERLAMBAT => 'Terlambat',
        self::STATUS_IZIN => 'Izin',
        self::STATUS_SAKIT => 'Sakit',
        self::STATUS_ALPHA => 'Alpha',
    ];

    protected $table = 'kehadiran';

    protected $fillable = [
        'sesi_presensi_id',
        'mahasiswa_id',
        'status',
        'waktu_scan',
        'catatan',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SesiPresensi::class, 'sesi_presensi_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
