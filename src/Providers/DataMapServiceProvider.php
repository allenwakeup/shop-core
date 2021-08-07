<?php

namespace Goodcatch\Modules\Core\Providers;

use Goodcatch\Modules\Core\Repositories\Admin\DataMapRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class DataMapServiceProvider extends ServiceProvider
{

    const CONFIG_KEY_DATA_MAPPING = 'core.data_mapping';
    const CONFIG_KEY_DATA_MAPPING_DEFINED = 'core.data_mapping.default';

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
        $config_repository = $this->app ['config'];
        if (! Arr::has ($config_repository->get (self::CONFIG_KEY_DATA_MAPPING), 'defined'))
        {
            if (App::environment ('testing', 'local')) {
                $data_maps = DataMapRepository::relationships ();
            } else {
                $data_maps = DataMapRepository::loadFromCache ();
            }

            $default = $config_repository->get (self::CONFIG_KEY_DATA_MAPPING_DEFINED);

            if (! empty ($default))
            {
                $data_maps = array_merge ($default, $data_maps);
            }

            if (isset ($data_maps) && count ($data_maps) > 0)
            {
                $config_repository->set (self::CONFIG_KEY_DATA_MAPPING_DEFINED, $data_maps);
            }
        }
    }
}
