<?php

namespace App\Jobs;

use App\Models\MarketDataLog;
use App\Services\AlphaVantageService;
use App\Services\CoinGeckoService;
use App\Services\FrankfurterService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class FetchAllMarketData implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $timeout = 120;

    private const REDIS_KEY = 'primeinvestor_data';

    public function __construct(
        private readonly FrankfurterService  $frankfurter,
        private readonly CoinGeckoService    $coinGecko,
        private readonly AlphaVantageService $alphaVantage,
    ) {}

    public function handle(): void
    {
        $payload = [
            'currencies'  => [],
            'crypto'      => [],
            'indices'     => [],
            'synced_at'   => now()->toIso8601String(),
        ];

        $this->run('frankfurter', function () use (&$payload) {
            $data = $this->frankfurter->fetch();
            $payload['currencies'] = $data;
            return count($data);
        });

        $this->run('coingecko', function () use (&$payload) {
            $data = $this->coinGecko->fetch();
            $payload['crypto'] = $data;
            return count($data);
        });

        $this->run('alphavantage', function () use (&$payload) {
            $data = $this->alphaVantage->fetch();
            $payload['indices'] = $data;
            return count($data);
        });

        // Write aggregated payload to Redis — dashboard reads only this key
        Redis::set(self::REDIS_KEY, json_encode($payload));

        Log::info('FetchAllMarketData: cache updated', ['key' => self::REDIS_KEY]);
    }

    // Executes a service fetch, logs the result, and swallows exceptions
    private function run(string $source, callable $callback): void
    {
        $recordCount  = 0;
        $success      = false;
        $errorMessage = null;

        try {
            $recordCount = (int) $callback();
            $success     = true;
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error("FetchAllMarketData [{$source}]: " . $e->getMessage());
        }

        MarketDataLog::create([
            'source'        => $source,
            'fetched_at'    => now(),
            'record_count'  => $recordCount,
            'success'       => $success,
            'error_message' => $errorMessage,
        ]);
    }
}
