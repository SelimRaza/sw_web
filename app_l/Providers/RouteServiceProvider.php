<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        $this->mapApiV1Routes();
        $this->mapApiV2Routes();
        $this->mapApiV3Routes();
        $this->mapApiV4Routes();
        $this->mapMokamApi();
        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    protected function mapApiV1Routes()
    {
        Route::prefix('api/v1')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_v1.php'));
    }


    /**
     * Define the "api/v2" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV2Routes()
    {
        Route::prefix('api/v2')
            ->middleware(['api'])
            ->namespace($this->namespace)
            ->group(base_path('routes/api_v2.php'));
    }
    protected function mapApiV3Routes()
    {
        Route::prefix('api/v3')
            ->middleware(['api'])
            ->namespace($this->namespace)
            ->group(base_path('routes/api_v3.php'));
    }
    protected function mapApiV4Routes()
    {
        Route::prefix('api/v4')
            ->middleware(['api'])
            ->namespace($this->namespace)
            ->group(base_path('routes/api_v4.php'));
    }
    protected function mapMokamApi()
    {
        Route::prefix('mokam')
            ->middleware(['api'])
            ->namespace($this->namespace)
            ->group(base_path('routes/mokam_api.php'));
    }
}
