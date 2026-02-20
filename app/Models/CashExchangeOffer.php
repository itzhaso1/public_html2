<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashExchangeOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'face_value',
        'cash_value',
        'currency',
        'enabled',
        'sort_order',
    ];

    protected $casts = [
        'enabled' => 'bool',
        'face_value' => 'int',
        'cash_value' => 'decimal:2',
        'sort_order' => 'int',
    ];
}

