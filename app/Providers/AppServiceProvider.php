<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Http\Middleware\Rabea;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\admin;
use App\Http\Middleware\CheckShop;
use App\Models\GoldItemsAvg;
use App\Observers\GoldItemsAvgObserver;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        Order::class => OrderPolicy::class,
    ];
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
        Route::aliasMiddleware('rabea', Rabea::class);
        Route::aliasMiddleware('user', CheckShop::class);
        Route::aliasMiddleware('admin', admin::class);
        GoldItemsAvg::observe(GoldItemsAvgObserver::class);
    }
}
