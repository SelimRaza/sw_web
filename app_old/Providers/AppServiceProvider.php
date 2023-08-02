<?php

namespace App\Providers;

use App\Menu\Menu;
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
        view()->composer('*', function ($view) {
            $menus = Menu::orderBy('wmnu_oseq', 'ASC')->get();
            view()->share('menus', $menus);
        });
    }
}
