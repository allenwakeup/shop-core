<?php

namespace Goodcatch\Modules\Core\Providers;

use Goodcatch\Modules\Core\Repositories\Admin\DataMapRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class DataMapServiceProvider extends ServiceProvider
{

    protected $map = [];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot ()
    {
        $this->loadDataMaps ();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register ()
    {

    }

    protected function loadDataMaps ()
    {
        if (! Arr::has ($this->app ['config']->get ('core.data_mapping'), 'defined'))
        {
            if (App::environment ('testing', 'local')) {
                $data_maps = DataMapRepository::allEnabled ();
            } else {
                $data_maps = DataMapRepository::loadFromCache ();
            }

            $default = $this->app ['config']->get ('core.data_mapping.default');

            if (! empty ($default))
            {
                $data_maps = array_merge ($default, $data_maps);
            }

            if (isset ($data_maps) && count ($data_maps) > 0)
            {
                $this->app ['config']->set ('core.data_mapping.defined', $data_maps);
            }
        }
    }
}
