<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\LoggingHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('LoggingHelper',function(){
            return new LoggingHelper();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}