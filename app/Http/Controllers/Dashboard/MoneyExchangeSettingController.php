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
        ]);

        return back()->with('success', 'تم حفظ الإعدادات ✅');
    }
}

