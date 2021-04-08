<?php

namespace Goodcatch\Modules\Core\Providers;

use Illuminate\Support\Facades\Route;
use Goodcatch\Modules\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Goodcatch\Modules\Core';

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map ()
    {
        $this->mapApiRoutes ();

        $this->mapWebRoutes ();

        $this->mapAdminRoutes ();
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes ()
    {
        if (app ()->has ('laravellocalization')) {
            $route = Route::middleware ('localeSessionRedirect', 'localizationRedirect', 'localeViewPath');
            $laravel_localization = app ('laravellocalization')->setLocale ();
            $route_file = module_path ('Core', 'routes/admin.php');
            if (! empty ($laravel_localization)) {
                $route->prefix ($laravel_localization);
                $route->group (function () use ($route_file)
                {
                    Route::prefix ('admin')
                        ->middleware ('web')
                        ->namespace ($this->moduleNamespace . '\\Http\\Controllers')
                        ->group ($route_file);
                });
            }
            else {
                Route::prefix ('admin')
                    ->middleware ('web')
                    ->namespace ($this->moduleNamespace . '\\Http\\Controllers')
                    ->group ($route_file);
            }

        }

    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes ()
    {
        Route::middleware ('web')
            ->namespace ($this->moduleNamespace . '\\' . $this->frontendNamespace)
            ->group (module_path ('Core', 'routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes ()
    {
        Route::prefix ('api')
            ->middleware ('api')
            ->namespace ($this->moduleNamespace . '\\' . $this->apiNamespace)
            ->group (module_path ('Core', 'routes/api.php'));
    }
}
