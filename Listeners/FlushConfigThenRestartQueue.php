<?php

namespace Goodcatch\Modules\Core\Listeners;

use Goodcatch\Modules\Core\Model\Admin\Schedule;

/**
 * Class FlushConnectionThenRestartQueue
 *
 * 清空Config，调用计划任务，重启队列
 *
 * 利用模块的计划任务机制，临时插入特别的任务，设置标志位为只执行一次，执行任务前恢复标志
 *
 * @package App\Modules\Core\Listeners
 */
abstract class FlushConfigThenRestartQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handler ()
    {
        $find = [
            'name' => self::class,
            'cron' => '* * * * *'
        ];
        $create = [
            'description' => 'Flush DataMap cache and then restart queue, DO NOT change it!',
            'once' => Schedule::ONCE_ENABLE,
            'group' => 'SYS',
            'status' => Schedule::STATUS_ENABLE
        ];
        foreach ([
            Schedule::firstOrCreate (array_merge ($find, ['input' => 'config:cache']),$create),
            Schedule::firstOrCreate (array_merge ($find, ['input' => 'queue:restart']),$create),
        ] as $order => $schedule) {
            if ($schedule->status === Schedule::STATUS_DISABLE)
            {
                $schedule->once = Schedule::ONCE_ENABLE;
                $schedule->status = Schedule::STATUS_ENABLE;
                $schedule->order = $order;
                $schedule->save ();
            }
        }
    }
}
