<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\PusatDataClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(protected PusatDataClient $pusatData) {}

    public function index(): View
    {
        $apiUrl = SystemSetting::get('pusat_data_api_url', config('services.pusat_data.url'));
        $apiToken = SystemSetting::get('pusat_data_api_token', config('services.pusat_data.token'));
        $apiTimeout = SystemSetting::get('pusat_data_api_timeout', config('services.pusat_data.timeout'));

        $connectionStatus = null;
        if ($apiToken) {
            try {
                $connectionStatus = $this->pusatData->ping() ? 'connected' : 'failed';
            } catch (\Exception $e) {
                $connectionStatus = 'error';
            }
        }

        return view('admin.settings.index', compact(
            'apiUrl',
            'apiToken',
            'apiTimeout',
            'connectionStatus'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pusat_data_api_url' => 'required|url',
            'pusat_data_api_token' => 'required|string',
            'pusat_data_api_timeout' => 'nullable|integer|min:5|max:60',
        ]);

        SystemSetting::set('pusat_data_api_url', $data['pusat_data_api_url'], 'URL API Pusat Data');
        SystemSetting::set('pusat_data_api_token', $data['pusat_data_api_token'], 'Token API Pusat Data');
        SystemSetting::set('pusat_data_api_timeout', $data['pusat_data_api_timeout'] ?? 10, 'Timeout API (detik)');

        return back()->with('success', 'Konfigurasi API berhasil disimpan.');
    }

    public function testConnection(): RedirectResponse
    {
        try {
            $result = $this->pusatData->ping();

            if ($result) {
                return back()->with('success', 'Koneksi ke Pusat Data berhasil!');
            }

            return back()->with('error', 'Koneksi gagal. Periksa URL dan token API.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
