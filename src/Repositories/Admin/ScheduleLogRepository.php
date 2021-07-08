<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Carbon\Carbon;
use Goodcatch\Modules\Core\Model\Admin\ScheduleLog;

class ScheduleLogRepository extends BaseRepository
{

    public static function add ($data)
    {
        return ScheduleLog::query ()->create ($data);
    }

    public static function recent ($perPage, $id, $keyword = null)
    {
        $data = ScheduleLog::query ()
            ->where ('schedule_id', $id)
            ->whereDate ('created_at', Carbon::yesterday ())
            ->where (function ($query) use ($keyword) {
                if (! empty ($keyword))
                {
                    $query->orWhere('content', 'like', "%$keyword%");
                }
            })
            ->orderBy ('created_at', 'desc')
            ->paginate ($perPage);
        return $data;
    }

}
