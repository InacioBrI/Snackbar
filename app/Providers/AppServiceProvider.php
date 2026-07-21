<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
use App\Services\Cart;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Cart::class, function ($app) {
            return new Cart($app['session.store']);
        });
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $view->with('storeSettings', [
                'name' => Setting::get('name', 'Lanchonete do Shopping'),
                'logo' => Setting::get('logo'),
                'phone' => Setting::get('phone'),
                'address' => Setting::get('address'),
                'hours' => Setting::get('hours'),
                'about' => Setting::get('about'),
                'service_fee_percent' => (float) Setting::get('service_fee_percent', 0),
                'payment_methods' => array_filter(explode(',', (string) Setting::get('payment_methods', 'pix,credit,debit'))),
            ]);

            $view->with('menuCategories', Schema::hasTable('categories')
                ? Category::where('is_active', true)->orderBy('sort_order')->get()
                : collect());

            $view->with('cart', app(Cart::class));
        });
    }
}
