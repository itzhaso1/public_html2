<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'publish_source')) {
                $table->string('publish_source', 20)->nullable()->after('client_number'); // public|admin|null
            }
            if (!Schema::hasColumn('products', 'review_note')) {
                $table->text('review_note')->nullable()->after('publish_source');
            }
            if (!Schema::hasColumn('products', 'review_reject_reasons')) {
                $table->text('review_reject_reasons')->nullable()->after('review_note'); // json
            }
            if (!Schema::hasColumn('products', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('review_reject_reasons');
            }
            if (!Schema::hasColumn('products', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
            if (!Schema::hasColumn('products', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('reviewed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            if (Schema::hasColumn('products', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
            if (Schema::hasColumn('products', 'reviewed_by')) {
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('products', 'review_reject_reasons')) {
                $table->dropColumn('review_reject_reasons');
            }
            if (Schema::hasColumn('products', 'review_note')) {
                $table->dropColumn('review_note');
            }
            if (Schema::hasColumn('products', 'publish_source')) {
                $table->dropColumn('publish_source');
            }
        });
    }
};

