<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('money_exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->unique();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // sar_to_usdt | usdt_to_sar
            $table->string('direction', 20);

            $table->decimal('amount_from', 12, 4);
            $table->decimal('amount_to', 12, 4);

            // Snapshot of rates used at request time
            $table->decimal('sar_per_usdt', 10, 4)->nullable();
            $table->decimal('profit_percent', 5, 2)->default(0);
            $table->decimal('usdt_to_sar_rate', 10, 4)->nullable();

            // Payout / receive details (encrypted)
            // For sar_to_usdt: destination_type + destination_value
            $table->string('destination_type', 30)->nullable(); // trc20|binance_id|email
            $table->text('destination_value')->nullable();

            // For usdt_to_sar: bank details (encrypted for numbers)
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->text('account_number')->nullable();
            $table->text('iban')->nullable();

            // pending|completed|rejected
            $table->string('status', 20)->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['direction', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_exchange_requests');
    }
};

