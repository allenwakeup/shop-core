<?php

namespace Goodcatch\Modules\Core\Listeners;

use Goodcatch\Modules\Core\Events\DataMapUpdated;
use Goodcatch\Modules\Core\Repositories\Admin\DataMapRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Class FlushConnectionThenRestartQueue
 *
 * 监听数据映射关系更新事件，清空数据映射的缓存，调用计划任务，重启队列
 *
 * 利用模块的计划任务机制，临时插入特别的任务，设置标志位为只执行一次，执行任务前恢复标志
 *
 * @package App\Modules\Core\Listeners
 */
class FlushDataMapThenRestartQueue extends FlushConfigThenRestartQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle (DataMapUpdated $event)
    {

        Cache::forget (config('modules.cache.key') . '.core.data_maps');
        Cache::forget (config('modules.cache.key') . '.core.data_maps');
        DataMapRepository::loadFromCache ();
        parent::handler ();
    }
}
