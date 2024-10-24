<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\ProviderInterface;
use Illuminate\Support\Arr;

class SocialiteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Socialite::extend('asgardeo', function ($app) {
            $config = $app['config']['services.asgardeo'];

            return Socialite::buildProvider(AsgardeoProvider::class, $config);
        });
    }
}
