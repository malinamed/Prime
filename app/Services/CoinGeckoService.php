<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoinGeckoService
{
    private const BASE_URL = 'https://api.coingecko.com/api/v3';

    // Fetches BTC and ETH prices in USD from CoinGecko free tier
    public function fetch(): array
    {
        $response = Http::timeout(10)
            ->get(self::BASE_URL . '/simple/price', [
                'ids'           => 'bitcoin,ethereum',
                'vs_currencies' => 'usd',
                'include_24hr_change' => 'true',
            ]);

        if ($response->failed()) {
            Log::warning('CoinGeckoService: request failed', ['status' => $response->status()]);
            return $this->fallback();
        }

        $data = $response->json();

        return [
            'BTC' => [
                'symbol'        => 'BTC',
                'name'          => 'Bitcoin',
                'price_usd'     => round((float) ($data['bitcoin']['usd'] ?? 0), 2),
                'change_24h'    => round((float) ($data['bitcoin']['usd_24h_change'] ?? 0), 2),
            ],
            'ETH' => [
                'symbol'        => 'ETH',
                'name'          => 'Ethereum',
                'price_usd'     => round((float) ($data['ethereum']['usd'] ?? 0), 2),
                'change_24h'    => round((float) ($data['ethereum']['usd_24h_change'] ?? 0), 2),
            ],
        ];
    }

    // Static fallback used when the API is unavailable
    private function fallback(): array
    {
        return [
            'BTC' => [
                'symbol'     => 'BTC',
                'name'       => 'Bitcoin',
                'price_usd'  => 67420.50,
                'change_24h' => 1.84,
            ],
            'ETH' => [
                'symbol'     => 'ETH',
                'name'       => 'Ethereum',
                'price_usd'  => 3521.75,
                'change_24h' => -0.62,
            ],
        ];
    }
}
