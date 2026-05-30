<?php

namespace App\Providers;

use App\Services\PusatDataClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PusatDataClient::class, function ($app) {
            $config = $app['config']->get('services.pusat_data');

            $url = \App\Models\SystemSetting::get('pusat_data_api_url', $config['url'] ?? '');
            $token = \App\Models\SystemSetting::get('pusat_data_api_token', $config['token'] ?? null);
            $timeout = (int) \App\Models\SystemSetting::get('pusat_data_api_timeout', $config['timeout'] ?? 10);

            return new PusatDataClient(
                baseUrl: rtrim($url, '/'),
                token: $token,
                timeout: $timeout,
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
