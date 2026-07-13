<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FrankfurterService
{
    private const BASE_URL = 'https://api.frankfurter.app';

    // Fetches EUR, USD, CHF rates against RON
    public function fetch(): array
    {
        $response = Http::timeout(10)
            ->get(self::BASE_URL . '/latest', [
                'to' => 'RON',
            ]);

        if ($response->failed()) {
            Log::warning('FrankfurterService: request failed', ['status' => $response->status()]);
            return $this->fallback();
        }

        $data = $response->json();
        $rates = $data['rates'] ?? [];

        return [
            'EUR' => [
                'from'  => 'EUR',
                'to'    => 'RON',
                'rate'  => round((float) ($rates['RON'] ?? 4.97), 4),
                'label' => 'Euro',
            ],
            'USD' => [
                'from'  => 'USD',
                'to'    => 'RON',
                'rate'  => round((float) ($this->fetchRate('USD') ?? 4.56), 4),
                'label' => 'US Dollar',
            ],
            'CHF' => [
                'from'  => 'CHF',
                'to'    => 'RON',
                'rate'  => round((float) ($this->fetchRate('CHF') ?? 5.14), 4),
                'label' => 'Swiss Franc',
            ],
        ];
    }

    // Fetches a single base currency rate against RON
    private function fetchRate(string $base): ?float
    {
        $response = Http::timeout(10)
            ->get(self::BASE_URL . '/latest', [
                'from' => $base,
                'to'   => 'RON',
            ]);

        if ($response->failed()) {
            return null;
        }

        return (float) ($response->json('rates.RON') ?? 0.0);
    }

    // Static fallback used when the API is unavailable
    private function fallback(): array
    {
        return [
            'EUR' => ['from' => 'EUR', 'to' => 'RON', 'rate' => 4.9700, 'label' => 'Euro'],
            'USD' => ['from' => 'USD', 'to' => 'RON', 'rate' => 4.5600, 'label' => 'US Dollar'],
            'CHF' => ['from' => 'CHF', 'to' => 'RON', 'rate' => 5.1400, 'label' => 'Swiss Franc'],
        ];
    }
}
