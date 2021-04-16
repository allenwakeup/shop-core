<?php

namespace Goodcatch\Modules\Core\Providers;

use Illuminate\Support\Facades\Route;
use Goodcatch\Modules\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Contracts\Container\BindingResolutionException;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Core';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'core';

    /**
     * internal module
     * @var mixed
     */
    protected $modulesInternal;

    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Goodcatch\Modules\Core';

    /**
     * RouteServiceProvider constructor.
     * @param $app
     * @throws BindingResolutionException
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->modulesInternal = $this->app->make('modules.internal');

    }

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
            $route_file = module_path($this->moduleName, 'routes/') . $this->modulesInternal->getLowerName() . '/admin.php';
            $namespace = $this->moduleNamespace . '\\Http\\Controllers\\' . $this->modulesInternal->getNamespace();
            if (! empty ($laravel_localization)) {
                $route->prefix ($laravel_localization);
                $route->group (function () use ($route_file, $namespace)
                {
                    Route::namespace ($namespace)
                        ->group ($route_file);
                });
            } else {
                Route::namespace ($namespace)
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
            ->namespace ($this->moduleNamespace . '\\Http\\Controllers\\' . $this->modulesInternal->getNamespace())
            ->group (module_path($this->moduleName, 'routes/' . $this->modulesInternal->getLowerName() . '/web.php'));
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
            ->namespace ($this->moduleNamespace . '\\Http\\Controllers\\' . $this->modulesInternal->getNamespace() . '\\Api')
            ->group (module_path($this->moduleName, 'routes/' . $this->modulesInternal->getLowerName() . '/api.php'));
    }
}
