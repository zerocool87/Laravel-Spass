<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherServiceConfigTest extends TestCase
{
    public function test_open_meteo_base_url_comes_from_config(): void
    {
        $expected = 'https://api.open-meteo.com/v1';

        $this->assertSame($expected, config('services.open_meteo.base_url'));
    }

    public function test_nominatim_base_url_comes_from_config(): void
    {
        $expected = 'https://nominatim.openstreetmap.org';

        $this->assertSame($expected, config('services.nominatim.base_url'));
    }

    public function test_weather_service_uses_config_urls(): void
    {
        $openMeteoUrl = config('services.open_meteo.base_url');
        $nominatimUrl = config('services.nominatim.base_url');

        Http::fake([
            $openMeteoUrl.'/forecast*' => Http::response([
                'current_weather' => ['weathercode' => 0, 'temperature' => 22],
            ]),
            $nominatimUrl.'/reverse*' => Http::response([
                'address' => ['city' => 'Limoges'],
            ]),
        ]);

        $service = $this->app->make(\App\Services\WeatherService::class);
        $result = $service->getWeather(45.83, 1.26, '');

        $this->assertSame('Limoges', $result['city']);
        $this->assertSame('☀️', $result['icon']);
        $this->assertSame('22°C', $result['temp']);
    }
}
