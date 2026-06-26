<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PusatDataClient
{
    public function __construct(
        protected string $baseUrl,
        protected ?string $token,
        protected int $timeout = 10,
    ) {}

    protected function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout($this->timeout)
            ->withToken($this->token);
    }

    public function findMahasiswa(string $nim): ?array
    {
        $response = $this->http()->get("mahasiswa/{$nim}");

        if ($response->status() === 404) {
            return null;
        }

        $response->throw();

        return $this->withUnit($response->json('data'));
    }

    /**
     * Check student permissions based on status
     */
    public function checkMahasiswaPermissions(string $nim): ?array
    {
        try {
            $response = $this->http()->get("mahasiswa/{$nim}/permissions");
            
            if ($response->status() === 404) {
                return null;
            }
            
            $response->throw();
            
            return $response->json('data');
        } catch (RequestException $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal cek permissions mahasiswa: ' . $e->getMessage());
            return null;
        }
    }

    public function findDosen(string $nip): ?array
    {
        $response = $this->http()->get("dosen/{$nip}");

        if ($response->status() === 404) {
            return null;
        }

        $response->throw();

        return $response->json('data');
    }

    public function ping(): bool
    {
        try {
            return $this->http()->get('me')->successful();
        } catch (RequestException) {
            return false;
        }
    }

    /**
     * @return array<int, array<string, mixed>> keyed by unit id
     */
    protected function unitsById(): array
    {
        return Cache::remember('pusat_data.units', now()->addMinutes(15), function () {
            try {
                $units = $this->http()->get('units')->throw()->json();
            } catch (RequestException) {
                return [];
            }

            return collect($units)->keyBy('id')->all();
        });
    }

    protected function withUnit(?array $mahasiswa): ?array
    {
        if (! $mahasiswa) {
            return $mahasiswa;
        }

        $unitId = $mahasiswa['unit_id'] ?? null;
        if (! $unitId) {
            return $mahasiswa;
        }

        $unit = $this->unitsById()[$unitId] ?? null;
        if ($unit) {
            $mahasiswa['unit'] = $unit;
        }

        return $mahasiswa;
    }

    public function kirimDataPresensi(array $dataKehadiran): bool
    {
        try {
            $response = $this->http()->post('presensi/kirim', $dataKehadiran);
            return $response->successful();
        } catch (RequestException $e) {
            // Log error untuk debugging (opsional)
            \Illuminate\Support\Facades\Log::warning('Gagal mengirim data presensi ke Pusat Data: ' . $e->getMessage());
            // Mengembalikan false jika Pusat Data sedang down, agar presensi lokal tidak error
            return false; 
        }
    }

    /**
     * Ambil data presensi dari Pusat Data untuk sinkronisasi
     */
    public function ambilDataPresensi(array $filter = []): ?array
    {
        try {
            $response = $this->http()->get('presensi/ambil', $filter);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (RequestException $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mengambil data presensi dari Pusat Data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cek status sinkronisasi dengan Pusat Data
     */
    public function cekStatusSinkronisasi(): ?array
    {
        try {
            $response = $this->http()->get('presensi/status');
            
            if ($response->successful()) {
                return $response->json('data');
            }
            
            return null;
        } catch (RequestException $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal cek status sinkronisasi: ' . $e->getMessage());
            return null;
        }
    }
}
