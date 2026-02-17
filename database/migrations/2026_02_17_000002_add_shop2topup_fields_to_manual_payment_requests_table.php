<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_payment_requests', function (Blueprint $table) {
            $table->string('shop2topup_trx_id')->nullable()->after('admin_note');
            $table->string('shop2topup_status')->nullable()->after('shop2topup_trx_id');
            $table->string('shop2topup_order_id')->nullable()->after('shop2topup_status');
            $table->string('shop2topup_secure_id', 128)->nullable()->after('shop2topup_order_id');
            $table->timestamp('shop2topup_delivery_at')->nullable()->after('shop2topup_secure_id');
            $table->json('shop2topup_response')->nullable()->after('shop2topup_delivery_at');
        });
    }

    public function down(): void
    {
        Schema::table('manual_payment_requests', function (Blueprint $table) {
            $table->dropColumn([
                'shop2topup_trx_id',
                'shop2topup_status',
                'shop2topup_order_id',
                'shop2topup_secure_id',
                'shop2topup_delivery_at',
                'shop2topup_response',
            ]);
        });
    }
};

