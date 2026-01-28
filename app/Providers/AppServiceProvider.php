<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
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
        Gate::before(function ($user, $ability) {
            return $user->is_admin ? true : null;
        });

        View::composer(['layouts.navigation', 'layouts.modernize'], function ($view) use ($navService) {
            $view->with('navItems', $navService->getMenuItems());
        });
    }
}
