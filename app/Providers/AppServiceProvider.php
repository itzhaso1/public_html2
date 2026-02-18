<?php

namespace App\Providers;

use App\Models\CashExchangeRequest;
use App\Models\Category;
use App\Models\ManualPaymentRequest;
use App\Models\MoneyExchangeRequest;
use App\Models\Order;
use App\Models\Setting;
use App\Observers\NewDashboardRequestWhatsAppObserver;
use App\Services\Currency\ExchangeRateService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $settings = Cache::remember('app_settings', 60 * 60, function () {
                    return Setting::with('media')->latest()->first();
                });

                if ($settings) {
                    $logo = $settings->getMediaUrl('setting', $settings, null, 'media', 'logo');
                    $favicon = $settings->getMediaUrl('setting', $settings, null, 'media', 'favicon');

                    View::share([
                        'settings' => $settings,
                        'logo' => $logo,
                        'favicon' => $favicon,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // If DB is misconfigured/unavailable, don't crash the whole app at boot.
        }

        // Share cached menu categories for website views
        View::composer('website.*', function ($view) {
            $fx = app(ExchangeRateService::class)->sarRates();
            $ratesByCountry = [
                'SA' => 1.0,
                'JO' => (float) ($fx['JOD'] ?? 0.1885),
                'US' => (float) ($fx['USD'] ?? 0.2666),
            ];

            try {
                $locale = app()->getLocale();
                $categories = Cache::remember("website.categories.menu.$locale", 60 * 10, function () {
                    return Category::with(['translations', 'media', 'children.translations'])
                        ->whereNull('parent_id')
                        ->where('status', 'active')
                        ->get();
                });
            } catch (\Throwable $e) {
                $categories = collect();
            }

            $view->with([
                'categories' => $categories,
                'currencyRatesByCountry' => $ratesByCountry,
                'currencyRatesMeta' => [
                    'base' => 'SAR',
                    'date' => $fx['date'] ?? null,
                    'source' => $fx['source'] ?? null,
                ],
            ]);
        });

        // WhatsApp notifications for new dashboard requests (best-effort)
        try {
            $observer = NewDashboardRequestWhatsAppObserver::class;
            ManualPaymentRequest::observe($observer);
            CashExchangeRequest::observe($observer);
            MoneyExchangeRequest::observe($observer);
            Order::observe($observer);
        } catch (\Throwable $e) {
            // ignore
        }
    }
}
