<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'money_exchange_enabled')) {
                $table->boolean('money_exchange_enabled')->default(true)->after('cash_exchange_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'money_exchange_enabled')) {
                $table->dropColumn('money_exchange_enabled');
            }
        });
    }
};

