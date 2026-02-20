<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_exchange_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_exchange_requests', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('completed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cash_exchange_requests', function (Blueprint $table) {
            if (Schema::hasColumn('cash_exchange_requests', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
        });
    }
};

