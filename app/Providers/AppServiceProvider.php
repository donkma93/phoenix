<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Pagination\Paginator;
use \Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
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
    public function boot(UrlGenerator $url)
    {
        //
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        if(env('PRODUCTION')) {
            URL::forceScheme('https');
            // $url->forceScheme('https');
        }



        \Shippo::setApiKey($this->app['config']['services.shippo.key']);
    }
}
