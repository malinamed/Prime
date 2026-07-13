<?php

use Illuminate\Support\Facades\Schedule;

// Run the market data sync every 15 minutes
Schedule::job(\App\Jobs\FetchAllMarketData::class)->everyFifteenMinutes();
