<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketDataLog extends Model
{
    protected $fillable = [
        'source',
        'fetched_at',
        'record_count',
        'success',
        'error_message',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
        'success'    => 'boolean',
    ];
}
