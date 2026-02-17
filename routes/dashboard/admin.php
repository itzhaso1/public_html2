<?php
 
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
 
/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/
 
Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
    Route::group(['middleware' => 'auth:admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {
        
        Route::resource('admins', Dashboard\AdminController::class);
        Route::get('/link-password', [Dashboard\AdminController::class, 'showForm'])->name('link_password.form');
        Route::post('/link-password', [Dashboard\AdminController::class, 'verify'])->name('link_password.verify');
        
        Route::controller(Dashboard\MainSettingsController::class)->prefix('mainSettings')->as('mainSettings.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::get('histories', 'history')->name('histories');
        });
        
        // =======================================================
        // ✅ (هام) تم إضافة رابط إضافة الشحن هنا (قبل products)
        // =======================================================
        Route::get('charge-items/create', [Dashboard\ProductController::class, 'createChargeProduct'])->name('products.create_charge');
        Route::post('charge-items/store', [Dashboard\ProductController::class, 'storeChargeProduct'])->name('products.store_charge');
        Route::post('charge-items/sync-offers', [Dashboard\ProductController::class, 'syncChargeOffers'])->name('products.sync_charge_offers');
 
        // صفحات منفصلة لقوائم المنتجات (حسب النوع)
        // IMPORTANT: must be before Route::resource('products') so it doesn't match products/{product}
        Route::get('products/accounts', [Dashboard\ProductController::class, 'accounts'])->name('products.accounts');
        Route::get('products/charge', [Dashboard\ProductController::class, 'charge'])->name('products.charge');
        Route::get('products/codes', [Dashboard\ProductController::class, 'codes'])->name('products.codes');

        // الروابط الأصلية للمنتجات
        Route::resource('products', Dashboard\ProductController::class);
        Route::post('products/import', [Dashboard\ProductController::class, 'import'])->name('products.import');
        Route::post('products/test-erp-connection', [Dashboard\ProductController::class, 'exportProductsToERP'])->name('test-erp-connection');

        // التصنيفات (الأقسام)
        Route::resource('categories', Dashboard\CategoryController::class);
        Route::post('categories/import', [Dashboard\CategoryController::class, 'import'])->name('categories.import');

        // أقسام الصفحة الرئيسية (Sections)
        Route::resource('sections', Dashboard\SectionController::class);

        Route::resource('users', Dashboard\UserController::class)->names('user');

        Route::prefix('manual-payments')->as('manual_payments.')->group(function () {
            Route::get('/', [Dashboard\ManualPaymentController::class, 'index'])->name('index');
            Route::get('{manualPaymentRequest}', [Dashboard\ManualPaymentController::class, 'show'])->name('show');
            Route::get('{manualPaymentRequest}/receipt', [Dashboard\ManualPaymentController::class, 'receipt'])->name('receipt');
            Route::post('{manualPaymentRequest}/approve', [Dashboard\ManualPaymentController::class, 'approve'])->name('approve');
            Route::post('{manualPaymentRequest}/reject', [Dashboard\ManualPaymentController::class, 'reject'])->name('reject');
        });

        Route::prefix('diamond-codes')->as('diamond_codes.')->group(function () {
            Route::get('/', [Dashboard\DiamondCodeController::class, 'index'])->name('index');
            Route::get('create', [Dashboard\DiamondCodeController::class, 'create'])->name('create');
            Route::post('/', [Dashboard\DiamondCodeController::class, 'store'])->name('store');
            Route::get('{diamondCode}/image', [Dashboard\DiamondCodeController::class, 'image'])->name('image');
            Route::delete('{diamondCode}', [Dashboard\DiamondCodeController::class, 'destroy'])->name('destroy');

            // Manage "codes" products quickly from the codes inventory screen
            Route::patch('product/{product}', [Dashboard\DiamondCodeController::class, 'updateProduct'])->name('product.update');
            Route::delete('product/{product}', [Dashboard\DiamondCodeController::class, 'destroyProduct'])->name('product.destroy');
        });
        
        Route::get('dashboard', Dashboard\DashboardController::class)->name('dashboard');
    });
});