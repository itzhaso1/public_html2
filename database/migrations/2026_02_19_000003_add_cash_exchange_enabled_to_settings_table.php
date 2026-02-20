<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'cash_exchange_enabled')) {
                $table->boolean('cash_exchange_enabled')->default(true)->after('home_quick_money_exchange_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'cash_exchange_enabled')) {
                $table->dropColumn('cash_exchange_enabled');
            }
        });
    }
};

