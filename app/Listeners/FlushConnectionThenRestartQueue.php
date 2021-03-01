<?php

namespace Goodcatch\Modules\Core\Listeners;

use Goodcatch\Modules\Core\Events\ConnectionUpdated;
use Goodcatch\Modules\Core\Repositories\Admin\ConnectionRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Class FlushConnectionThenRestartQueue
 *
 * 监听数据源连接更新事件，清空数据连接配置的缓存，调用计划任务，重启队列
 *
 * 利用模块的计划任务机制，临时插入特别的任务，设置标志位为只执行一次，执行任务前恢复标志
 *
 * @package App\Modules\Core\Listeners
 */
class FlushConnectionThenRestartQueue extends FlushConfigThenRestartQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle (ConnectionUpdated $event)
    {

        Cache::forget (config('modules.cache.key') . '.core.connections');
        ConnectionRepository::all ();
        parent::handler ();
    }
}
