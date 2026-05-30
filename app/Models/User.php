<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_DOSEN = 'dosen';
    public const ROLE_MAHASISWA = 'mahasiswa';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'username',
        'role',
        'nama',
        'program_studi',
        'fakultas',
        'jabatan',
        'password',
        'last_synced_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function isDosen(): bool
    {
        return $this->role === self::ROLE_DOSEN;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === self::ROLE_MAHASISWA;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function kelasDiampu(): HasMany
    {
        return $this->hasMany(Kelas::class, 'dosen_id');
    }

    public function kelasDiikuti(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_mahasiswa', 'mahasiswa_id', 'kelas_id')
            ->withTimestamps();
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class, 'mahasiswa_id');
    }
}
