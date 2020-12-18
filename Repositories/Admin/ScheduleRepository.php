<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class ScheduleRepository extends BaseRepository
{
    public static function list ($perPage, $condition = [], $keyword = null)
    {

        $jobs = \collect (light_dictionary('CORE_DICT_SCHEDULE_JOBS'))->reduce(
            function ($arr, $item) {
                $arr [$item ['id']] = $item ['name'];
                return $arr;
            }, []
        );

        $data = Schedule::query ()
            ->with (['logs' => function ($query) {
                $query
                    ->whereDate ('created_at', Carbon::today ())
                    ->where ('type', 1)
                    ->orderBy ('created_at', 'desc');
            }])
            ->where (function ($query) use ($condition, $keyword) {
                self::buildQuery ($query, $condition);
                if (! empty ($keyword))
                {
                    self::buildSelect ($query, $condition, $keyword);
                }
            })
            ->orderBy ('order', 'desc')
            ->paginate ($perPage);

        $data->transform (function ($item) use ($jobs) {
            $item->editUrl = route ('admin::' . module_route_prefix ('.') . 'core.schedule.edit', ['id' => $item->id]);
            $item->deleteUrl = route ('admin::' . module_route_prefix ('.') . 'core.schedule.delete', ['id' => $item->id]);
            $item->detailUrl = route ('admin::' . module_route_prefix ('.') . 'core.schedule.detail', ['id' => $item->id]);
            if (Arr::has ($jobs, $item->input))
            {
                $item->inputText = 'Queue ' . Arr::get ($jobs, $item->input, '--');
            }
            $item->schedule_type_text = Arr::get (Schedule::TYPE, $item->schedule_type, '--');
            return $item;
        });

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data->total (),
            'data' => $data->items (),
        ];
    }

    public static function add ($data)
    {
        self::transform (self::TRANSFORM_TYPE_JSON, $data, 'payload');
        return Schedule::query ()->create ($data);
    }

    public static function update ($id, $data)
    {
        self::transform (self::TRANSFORM_TYPE_JSON, $data, 'payload');
        return Schedule::find ($id)->update ($data);
    }

    public static function find ($id)
    {
        return Schedule::query ()->find ($id);
    }

    public static function delete ($id)
    {
        return Schedule::destroy ($id);
    }

    public static function groupNames()
    {
        return Schedule::query()->select('group')->distinct()->pluck('group')->toArray();
    }

}
