<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    // Reads ONLY from Redis — no DB queries, no direct API calls
    public function index(Request $request): Response
    {
        $raw = Redis::get('primeinvestor_data');

        $marketData = $raw ? json_decode($raw, true) : null;

        return Inertia::render('Dashboard', [
            'marketData' => $marketData,
        ]);
    }
}
