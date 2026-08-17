<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Commission;
use App\Models\Product;
use App\Models\User;
use App\Observers\CartObserver;
use App\Observers\CommissionObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use App\Support\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Catat aktivitas otomatis dari perubahan model
        Product::observe(ProductObserver::class);
        User::observe(UserObserver::class);
        Cart::observe(CartObserver::class);
        Commission::observe(CommissionObserver::class);

        // Catat login & logout
        Event::listen(Login::class, function (Login $event): void {
            ActivityLogger::log($event->user->id, 'login', 'Login ke sistem.');
        });

        Event::listen(Logout::class, function (Logout $event): void {
            ActivityLogger::log($event->user->id, 'logout', 'Logout dari sistem.');
        });
    }
}
