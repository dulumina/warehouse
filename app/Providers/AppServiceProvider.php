<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\NavigationService;

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
    public function boot(NavigationService $navService): void
    {
        View::composer(['layouts.navigation', 'layouts.modernize'], function ($view) use ($navService) {
            $view->with('navItems', $navService->getMenuItems());
        });
    }
}
