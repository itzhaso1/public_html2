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
        'receive_sar_bank_name',
        'receive_sar_account_name',
        'receive_sar_account_number',
        'receive_sar_iban',
        'receive_sar_note',
        'receive_usdt_trc20_address',
        'receive_usdt_binance_id',
        'receive_usdt_note',
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
        'receive_sar_account_number' => 'encrypted',
        'receive_sar_iban' => 'encrypted',
        'receive_usdt_trc20_address' => 'encrypted',
    ];
}

