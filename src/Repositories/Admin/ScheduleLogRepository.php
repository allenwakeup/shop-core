<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\ScheduleLog;

class ScheduleLogRepository
{

    public static function add ($data)
    {
        return ScheduleLog::query ()->create ($data);
    }

}
