<?php

namespace App\Providers;

use App\Models\MotorCycle;
use App\Observers\MotorcycleObserver;
use Illuminate\Support\ServiceProvider;

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
    public function boot()
    {
           MotorCycle::observe(MotorcycleObserver::class);
    }
}
