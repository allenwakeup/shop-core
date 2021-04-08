<?php

namespace Goodcatch\Modules\Core\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class ConnectionUpdated
 *
 * 数据映射关系被更新触发事件
 *
 * @package Goodcatch\Modules\Core\Events
 */
class DataMapUpdated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct ()
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn ()
    {
        return [];
    }
}
