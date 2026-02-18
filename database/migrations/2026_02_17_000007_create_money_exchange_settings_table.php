<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('money_exchange_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);

            // Customer pays SAR to receive 1 USDT (e.g. 4.00 SAR per 1 USDT)
            $table->decimal('sar_per_usdt', 10, 4)->nullable();

            // Profit spread percent used to compute USDT->SAR rate (e.g. 6.25% => 4.00 * (1-0.0625)=3.75)
            $table->decimal('profit_percent', 5, 2)->default(0);

            // Customer gives 1 USDT to receive SAR (computed from sar_per_usdt & profit_percent)
            $table->decimal('usdt_to_sar_rate', 10, 4)->nullable();

            // Limits
            $table->decimal('min_sar', 12, 2)->default(0);
            $table->decimal('max_sar', 12, 2)->default(0);
            $table->decimal('min_usdt', 12, 4)->default(0);
            $table->decimal('max_usdt', 12, 4)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_exchange_settings');
    }
};

