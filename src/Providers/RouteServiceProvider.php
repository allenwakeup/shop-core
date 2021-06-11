<?php

namespace Goodcatch\Modules\Core\Providers;

use Goodcatch\Modules\Qwshop\Providers\RouteServiceProvider as ServiceProvider;

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
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = 'Goodcatch\\Modules\\Core\\Http\\Controllers\\';

}
