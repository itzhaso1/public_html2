<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashExchangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'offer_id',
        'face_value',
        'cash_value',
        'currency',
        'card_code',
        'bank_name',
        'account_name',
        'account_number',
        'iban',
        'status',
        'admin_note',
        'completed_at',
    ];

    protected $casts = [
        'face_value' => 'int',
        'cash_value' => 'decimal:2',
        'completed_at' => 'datetime',
        // Laravel built-in encrypted cast (stores encrypted string in DB)
        'card_code' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(CashExchangeOffer::class, 'offer_id');
    }
}

