<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyExchangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'direction',
        'amount_from',
        'amount_to',
        'sar_per_usdt',
        'profit_percent',
        'usdt_to_sar_rate',
        'destination_type',
        'destination_value',
        'bank_name',
        'account_name',
        'account_number',
        'iban',
        'status',
        'admin_note',
        'completed_at',
        'rejected_at',
    ];

    protected $casts = [
        'amount_from' => 'decimal:4',
        'amount_to' => 'decimal:4',
        'sar_per_usdt' => 'decimal:4',
        'profit_percent' => 'decimal:2',
        'usdt_to_sar_rate' => 'decimal:4',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
        // Encrypt sensitive fields
        'destination_value' => 'encrypted',
        'account_number' => 'encrypted',
        'iban' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

