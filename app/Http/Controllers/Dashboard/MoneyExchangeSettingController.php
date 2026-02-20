<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MoneyExchangeSetting;
use Illuminate\Http\Request;

class MoneyExchangeSettingController extends Controller
{
    public function edit()
    {
        $settings = MoneyExchangeSetting::query()->latest('id')->first() ?? MoneyExchangeSetting::create([
            'enabled' => false,
            'profit_percent' => 0,
            'min_sar' => 0,
            'max_sar' => 0,
            'min_usdt' => 0,
            'max_usdt' => 0,
        ]);

        return view('dashboard.admin.money_exchange.settings.edit', [
            'pageTitle' => 'إعدادات تحويل الأموال (SAR ↔ USDT)',
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $settings = MoneyExchangeSetting::query()->latest('id')->firstOrFail();

        $data = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'sar_per_usdt' => ['required', 'numeric', 'gt:0'],
            'profit_percent' => ['required', 'numeric', 'min:0', 'max:50'],
            'min_sar' => ['nullable', 'numeric', 'min:0'],
            'max_sar' => ['nullable', 'numeric', 'min:0'],
            'min_usdt' => ['nullable', 'numeric', 'min:0'],
            'max_usdt' => ['nullable', 'numeric', 'min:0'],

            // Transfer info
            'receive_sar_bank_name' => ['nullable', 'string', 'max:190'],
            'receive_sar_account_name' => ['nullable', 'string', 'max:190'],
            'receive_sar_account_number' => ['nullable', 'string', 'max:500'],
            'receive_sar_iban' => ['nullable', 'string', 'max:500'],
            'receive_sar_note' => ['nullable', 'string', 'max:1000'],
            'receive_usdt_trc20_address' => ['nullable', 'string', 'max:500'],
            'receive_usdt_binance_id' => ['nullable', 'string', 'max:190'],
            'receive_usdt_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $sarPerUsdt = (float) $data['sar_per_usdt'];
        $profit = (float) $data['profit_percent'];
        $usdtToSar = $sarPerUsdt * (1 - ($profit / 100));
        if ($usdtToSar <= 0) {
            return back()->withErrors(['profit_percent' => 'نسبة الربح كبيرة جدًا.'])->withInput();
        }

        $settings->update([
            'enabled' => (bool) ($data['enabled'] ?? false),
            'sar_per_usdt' => $sarPerUsdt,
            'profit_percent' => $profit,
            'usdt_to_sar_rate' => round($usdtToSar, 4),
            'min_sar' => (float) ($data['min_sar'] ?? 0),
            'max_sar' => (float) ($data['max_sar'] ?? 0),
            'min_usdt' => (float) ($data['min_usdt'] ?? 0),
            'max_usdt' => (float) ($data['max_usdt'] ?? 0),

            'receive_sar_bank_name' => isset($data['receive_sar_bank_name']) ? trim((string) $data['receive_sar_bank_name']) : null,
            'receive_sar_account_name' => isset($data['receive_sar_account_name']) ? trim((string) $data['receive_sar_account_name']) : null,
            'receive_sar_account_number' => isset($data['receive_sar_account_number']) ? (trim((string) $data['receive_sar_account_number']) ?: null) : null,
            'receive_sar_iban' => isset($data['receive_sar_iban']) ? (trim((string) $data['receive_sar_iban']) ?: null) : null,
            'receive_sar_note' => isset($data['receive_sar_note']) ? (trim((string) $data['receive_sar_note']) ?: null) : null,

            'receive_usdt_trc20_address' => isset($data['receive_usdt_trc20_address']) ? (trim((string) $data['receive_usdt_trc20_address']) ?: null) : null,
            'receive_usdt_binance_id' => isset($data['receive_usdt_binance_id']) ? (trim((string) $data['receive_usdt_binance_id']) ?: null) : null,
            'receive_usdt_note' => isset($data['receive_usdt_note']) ? (trim((string) $data['receive_usdt_note']) ?: null) : null,
        ]);

        return back()->with('success', 'تم حفظ الإعدادات ✅');
    }
}

