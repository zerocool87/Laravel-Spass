<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    /** @return array{icon: string, temp: string, city: string} */
    public function getWeather(float $lat, float $lng, string $defaultCity = ''): array
    {
        $cacheKey = "weather_{$lat}_{$lng}";

        return Cache::remember($cacheKey, 1800, function () use ($lat, $lng, $defaultCity) {
            $weather = $this->fetchWeather($lat, $lng);
            $city = $defaultCity ?: $this->fetchCity($lat, $lng);

            return array_merge($weather, ['city' => $city]);
        });
    }

    /** @return array{icon: string, temp: string} */
    private function fetchWeather(float $lat, float $lng): array
    {
        try {
            $response = Http::timeout(5)->get(config('services.open_meteo.base_url').'/forecast', [
                'latitude' => $lat,
                'longitude' => $lng,
                'current_weather' => true,
                'timezone' => 'auto',
            ]);

            if ($response->failed()) {
                return ['icon' => '❓', 'temp' => '--'];
            }

            $data = $response->json();
            $code = $data['current_weather']['weathercode'] ?? 0;
            $temp = round($data['current_weather']['temperature'] ?? 0);

            $icon = match (true) {
                $code === 0 => '☀️',
                $code <= 3 => '⛅',
                $code >= 95 => '⛈️',
                $code >= 80 => '🌦️',
                $code >= 71 => '❄️',
                $code >= 61 => '🌧️',
                $code >= 51 => '🌦️',
                $code >= 45 => '🌫️',
                default => '☀️',
            };

            return ['icon' => $icon, 'temp' => $temp.'°C'];
        } catch (RequestException|ConnectionException) {
            return ['icon' => '❓', 'temp' => '--'];
        }
    }

    private function fetchCity(float $lat, float $lng): string
    {
        try {
            $response = Http::timeout(5)
                ->withUserAgent(config('services.nominatim.user_agent'))
                ->get(config('services.nominatim.base_url').'/reverse', [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $lng,
                    'zoom' => 12,
                    'accept-language' => 'fr',
                ]);

            if ($response->failed()) {
                return '';
            }

            $addr = $response->json('address');

            if (! $addr) {
                return '';
            }

            return $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['municipality'] ?? $addr['county'] ?? '';
        } catch (RequestException|ConnectionException) {
            return '';
        }
    }
}
