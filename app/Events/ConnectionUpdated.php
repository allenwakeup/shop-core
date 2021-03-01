<?php

namespace Goodcatch\Modules\Core\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class ConnectionUpdated
 *
 * 数据源连接被更新触发事件
 *
 * @package Goodcatch\Modules\Core\Events
 */
class ConnectionUpdated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
