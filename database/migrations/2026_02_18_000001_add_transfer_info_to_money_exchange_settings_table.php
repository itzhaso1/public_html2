<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('money_exchange_settings', function (Blueprint $table) {
            // Customer sends SAR to admin (for SAR -> USDT requests)
            $table->string('receive_sar_bank_name')->nullable()->after('max_usdt');
            $table->string('receive_sar_account_name')->nullable()->after('receive_sar_bank_name');
            $table->text('receive_sar_account_number')->nullable()->after('receive_sar_account_name');
            $table->text('receive_sar_iban')->nullable()->after('receive_sar_account_number');
            $table->text('receive_sar_note')->nullable()->after('receive_sar_iban');

            // Customer sends USDT to admin (for USDT -> SAR requests)
            $table->text('receive_usdt_trc20_address')->nullable()->after('receive_sar_note');
            $table->string('receive_usdt_binance_id')->nullable()->after('receive_usdt_trc20_address');
            $table->text('receive_usdt_note')->nullable()->after('receive_usdt_binance_id');
        });
    }

    public function down(): void
    {
        Schema::table('money_exchange_settings', function (Blueprint $table) {
            $table->dropColumn([
                'receive_sar_bank_name',
                'receive_sar_account_name',
                'receive_sar_account_number',
                'receive_sar_iban',
                'receive_sar_note',
                'receive_usdt_trc20_address',
                'receive_usdt_binance_id',
                'receive_usdt_note',
            ]);
        });
    }
};

