<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_exchange_offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Example: "سوا 20" -> face_value = 20
            $table->unsignedInteger('face_value');
            // Amount user receives in cash for this face value
            $table->decimal('cash_value', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_exchange_offers');
    }
};

