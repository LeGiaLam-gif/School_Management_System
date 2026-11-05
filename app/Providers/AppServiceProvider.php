<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Provider cap ung dung. Moi cau hinh rieng cua nghiep vu nam trong
 * ServiceProvider cua tung module (app/Modules/<Module>/Providers).
 */
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
        //
    }
}
