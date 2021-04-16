<?php

namespace Goodcatch\Modules\Core\Providers;

use Goodcatch\Modules\Core\Model\Admin\DataMap;
use Goodcatch\Modules\Core\Model\Admin\DataRoute;
use Goodcatch\Modules\Core\Observers\DataMapObserver;
use Goodcatch\Modules\Core\Observers\DataRouteObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * internal module
     * @var mixed
     */
    protected $modulesInternal;

    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Core';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'core';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));


        DataMap::observe (DataMapObserver::class);
        DataRoute::observe (DataRouteObserver::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     * @throws \Exception
     */
    public function register()
    {

        $this->validateInternalModule();

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(DatabaseServiceProvider::class);
        $this->app->register(DataMapServiceProvider::class);

        $this->registerMailViews();

    }


    /**
     * make sure the internal module present
     *
     * @throws \Exception
     */
    protected function validateInternalModule () {
        $this->modulesInternal = $this->app->make ('modules.internal');

        if(!isset ($modules_internal)){
            throw new \Exception('Internal module ' . module_integration() . ' not found', "modules.internal");
        }
    }


    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'resources/views/' . module_integration());

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    public function registerMailViews()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                module_path($this->moduleName, 'resources/views/emails') => $this->app->resourcePath('views/emails'),
            ], 'core-email');
        }
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang/' . $this->modulesInternal->getLowerName()), $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
