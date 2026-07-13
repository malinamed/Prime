<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlphaVantageService
{
    private const BASE_URL = 'https://www.alphavantage.co/query';

    // ETF proxies: SPY tracks S&P 500, QQQ tracks Nasdaq-100
    private const SYMBOLS = [
        'SPY' => 'S&P 500 (SPY)',
        'QQQ' => 'Nasdaq-100 (QQQ)',
    ];

    // Fetches closing quote data for S&P 500 and Nasdaq-100 proxies
    public function fetch(): array
    {
        $apiKey = config('services.alpha_vantage.key');

        if (empty($apiKey)) {
            Log::info('AlphaVantageService: no API key configured, returning mock data');
            return $this->fallback();
        }

        $results = [];

        foreach (self::SYMBOLS as $symbol => $label) {
            $response = Http::timeout(15)
                ->get(self::BASE_URL, [
                    'function' => 'GLOBAL_QUOTE',
                    'symbol'   => $symbol,
                    'apikey'   => $apiKey,
                ]);

            if ($response->failed()) {
                Log::warning('AlphaVantageService: request failed', ['symbol' => $symbol]);
                $results[$symbol] = $this->fallbackForSymbol($symbol, $label);
                continue;
            }

            $quote = $response->json('Global Quote');

            // API returns empty quote on invalid key or rate limit
            if (empty($quote) || empty($quote['05. price'])) {
                Log::warning('AlphaVantageService: empty quote received', ['symbol' => $symbol]);
                $results[$symbol] = $this->fallbackForSymbol($symbol, $label);
                continue;
            }

            $price  = (float) $quote['05. price'];
            $change = (float) $quote['09. change'];
            $pct    = (float) rtrim($quote['10. change percent'] ?? '0%', '%');

            $results[$symbol] = [
                'symbol'         => $symbol,
                'label'          => $label,
                'price'          => round($price, 2),
                'change'         => round($change, 2),
                'change_percent' => round($pct, 2),
                'is_mock'        => false,
            ];
        }

        return $results;
    }

    // Realistic static data used when the API key is absent or the request fails
    private function fallback(): array
    {
        $data = [];
        foreach (self::SYMBOLS as $symbol => $label) {
            $data[$symbol] = $this->fallbackForSymbol($symbol, $label);
        }
        return $data;
    }

    private function fallbackForSymbol(string $symbol, string $label): array
    {
        $mock = [
            'SPY' => ['price' => 512.84, 'change' => 3.21, 'change_percent' => 0.63],
            'QQQ' => ['price' => 436.57, 'change' => -1.18, 'change_percent' => -0.27],
        ];

        return [
            'symbol'         => $symbol,
            'label'          => $label,
            'price'          => $mock[$symbol]['price'],
            'change'         => $mock[$symbol]['change'],
            'change_percent' => $mock[$symbol]['change_percent'],
            'is_mock'        => true,
        ];
    }
}
