<?php

namespace Goodcatch\Modules\Core\Contracts\Database;

use Goodcatch\Modules\Core\Model\Admin\Connection;
use Goodcatch\Modules\Laravel\Contracts\Database\DBConnectionProvider;
use Illuminate\Contracts\Foundation\Application;

class LightcmsConnectionProvider implements DBConnectionProvider
{

    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The provider driver name.
     *
     * @var string
     */
    protected $driver;

    /**
     * Create a new Auth manager instance.
     *
     * @param  Application  $app
     * @param  string $driver
     * @return void
     */
    public function __construct ($app, $driver)
    {
        $this->app = $app;
        $this->driver = $driver;
    }

    /**
     * @inheritDoc
     */
    public function all ()
    {
        return Connection::query ()
            ->with ('datasource')
            ->get ();
    }


    /**
     * @inheritDoc
     */
    public function find ($alias)
    {
        return Connection::query ()
            ->with ('datasource')
            ->where ('name', $alias)
            ->first ();
    }

    /**
     * @inheritDoc
     */
    public function getDriver ()
    {
        return $this->driver;
    }

}