<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    // Global props shared with every Inertia page
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
        ]);
    }
}
