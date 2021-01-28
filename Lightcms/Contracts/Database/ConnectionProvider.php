<?php

namespace Goodcatch\Modules\Core\Lightcms\Contracts\Database;

use Goodcatch\Modules\Core\Model\Admin\Connection;
use Goodcatch\Modules\Laravel\Contracts\Database\DBConnectionProvider;

class ConnectionProvider implements DBConnectionProvider
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
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
     * @param  \Illuminate\Contracts\Foundation\Application  $app
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