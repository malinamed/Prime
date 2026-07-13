<?php

namespace App\Console\Commands;

use App\Services\AlphaVantageService;
use App\Services\CoinGeckoService;
use App\Services\FrankfurterService;
use App\Jobs\FetchAllMarketData;
use Illuminate\Console\Command;

class SyncMarketData extends Command
{
    protected $signature   = 'market:sync';
    protected $description = 'Synchronously fetch all market data and populate the Redis cache';

    // Runs FetchAllMarketData inline (bypasses queue) for first-run bootstrap
    public function handle(
        FrankfurterService  $frankfurter,
        CoinGeckoService    $coinGecko,
        AlphaVantageService $alphaVantage,
    ): int {
        $this->info('Fetching market data...');

        $job = new FetchAllMarketData($frankfurter, $coinGecko, $alphaVantage);
        $job->handle();

        $this->info('Redis cache updated. Key: primeinvestor_data');

        return Command::SUCCESS;
    }
}
