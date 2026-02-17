<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->unique();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('offer_id')->constrained('cash_exchange_offers')->restrictOnDelete();

            $table->unsignedInteger('face_value');
            $table->decimal('cash_value', 10, 2);
            $table->string('currency', 3)->default('SAR');

            // Sensitive: store encrypted via model cast
            $table->text('card_code');

            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();

            $table->string('status', 20)->default('pending'); // pending|completed
            $table->text('admin_note')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_exchange_requests');
    }
};

