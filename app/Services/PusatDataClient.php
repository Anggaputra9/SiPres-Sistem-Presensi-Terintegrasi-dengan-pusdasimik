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
}
