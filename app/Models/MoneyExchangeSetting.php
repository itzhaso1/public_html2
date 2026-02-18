<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyExchangeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'enabled',
        'sar_per_usdt',
        'profit_percent',
        'usdt_to_sar_rate',
        'min_sar',
        'max_sar',
        'min_usdt',
        'max_usdt',
    ];

    protected $casts = [
        'enabled' => 'bool',
        'sar_per_usdt' => 'decimal:4',
        'profit_percent' => 'decimal:2',
        'usdt_to_sar_rate' => 'decimal:4',
        'min_sar' => 'decimal:2',
        'max_sar' => 'decimal:2',
        'min_usdt' => 'decimal:4',
        'max_usdt' => 'decimal:4',
    ];
}

