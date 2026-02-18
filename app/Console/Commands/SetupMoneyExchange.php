<?php

namespace App\Console\Commands;

use App\Models\MoneyExchangeSetting;
use Illuminate\Console\Command;

class SetupMoneyExchange extends Command
{
    protected $signature = 'money-exchange:setup
                            {--sar-per-usdt= : SAR per 1 USDT (e.g. 4.00)}
                            {--profit= : Profit percent for USDT->SAR (e.g. 5)}
                            {--enable : Enable service}';

    protected $description = 'Setup SAR↔USDT exchange rates and profit';

    public function handle(): int
    {
        $sarPerUsdt = (float) ($this->option('sar-per-usdt') ?? 0);
        $profit = (float) ($this->option('profit') ?? 0);
        $enable = (bool) $this->option('enable');

        if ($sarPerUsdt <= 0) {
            $this->error('Invalid --sar-per-usdt. Example: --sar-per-usdt=4.00');
            return self::FAILURE;
        }
        if ($profit < 0 || $profit > 50) {
            $this->error('Invalid --profit. Must be between 0 and 50.');
            return self::FAILURE;
        }

        $usdtToSar = $sarPerUsdt * (1 - ($profit / 100));
        if ($usdtToSar <= 0) {
            $this->error('Computed USDT->SAR rate is invalid. Reduce profit.');
            return self::FAILURE;
        }

        $settings = MoneyExchangeSetting::query()->latest('id')->first();
        if (! $settings) {
            $settings = MoneyExchangeSetting::create([
                'enabled' => false,
                'profit_percent' => 0,
                'min_sar' => 0,
                'max_sar' => 0,
                'min_usdt' => 0,
                'max_usdt' => 0,
            ]);
        }

        $settings->update([
            'enabled' => $enable ? true : $settings->enabled,
            'sar_per_usdt' => $sarPerUsdt,
            'profit_percent' => $profit,
            'usdt_to_sar_rate' => round($usdtToSar, 4),
        ]);

        $this->info('Money exchange settings updated ✅');
        $this->line('SAR per 1 USDT: ' . number_format($sarPerUsdt, 4));
        $this->line('Profit %: ' . number_format($profit, 2));
        $this->line('USDT -> SAR rate: ' . number_format($settings->usdt_to_sar_rate, 4));
        $this->line('Enabled: ' . ($settings->enabled ? 'yes' : 'no'));

        return self::SUCCESS;
    }
}

