<?php

namespace Goodcatch\Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Goodcatch\Modules\Core\Events\ConnectionUpdated' => [
            'Goodcatch\Modules\Core\Listeners\FlushConnectionThenRestartQueue',
        ],
        'Goodcatch\Modules\Core\Events\DataMapUpdated' => [
            'Goodcatch\Modules\Core\Listeners\FlushDataMapThenRestartQueue',
        ],
    ];
}
