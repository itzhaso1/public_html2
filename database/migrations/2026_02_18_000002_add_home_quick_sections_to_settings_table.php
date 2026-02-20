<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('home_quick_charge_title')->nullable()->after('version');
            $table->string('home_quick_codes_title')->nullable()->after('home_quick_charge_title');
            $table->string('home_quick_cash_exchange_title')->nullable()->after('home_quick_codes_title');
            $table->string('home_quick_money_exchange_title')->nullable()->after('home_quick_cash_exchange_title');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_quick_charge_title',
                'home_quick_codes_title',
                'home_quick_cash_exchange_title',
                'home_quick_money_exchange_title',
            ]);
        });
    }
};

