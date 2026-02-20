<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeProductServiceType extends Command
{
    protected $signature = 'products:normalize-service-type {--dry-run : Show counts without writing}';

    protected $description = 'Normalize products.service_type (codes/gems/null) based on existing data';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $this->info('Normalizing products.service_type ...');

        // codes: if product has any diamond codes
        $codesIds = DB::table('diamond_codes')->select('product_id')->distinct();
        $codesCount = DB::table('products')->whereIn('id', $codesIds)->count();

        // gems: if product has vendor offer id (itemID) set
        $gemsCount = DB::table('products')
            ->whereNotNull('itemID')
            ->where('itemID', '!=', '')
            ->count();

        $this->line("Detected products with diamond codes: {$codesCount}");
        $this->line("Detected products with itemID (offers): {$gemsCount}");

        if ($dry) {
            $this->warn('Dry run mode; no changes written.');
            return self::SUCCESS;
        }

        DB::beginTransaction();
        try {
            $updatedCodes = DB::table('products')
                ->whereIn('id', $codesIds)
                ->update(['service_type' => 'codes']);

            $updatedGems = DB::table('products')
                ->whereNotNull('itemID')
                ->where('itemID', '!=', '')
                ->update(['service_type' => 'gems']);

            // Any other value becomes NULL (accounts)
            $cleared = DB::table('products')
                ->whereNotIn('service_type', ['codes', 'gems'])
                ->update(['service_type' => null]);

            DB::commit();

            $this->info("Updated codes: {$updatedCodes}");
            $this->info("Updated gems: {$updatedGems}");
            $this->info("Cleared to NULL (accounts): {$cleared}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}

