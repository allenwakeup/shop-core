<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Http\Resources\Admin\ScheduleResource\ScheduleCollection;
use Goodcatch\Modules\Core\Http\Requests\Admin\ScheduleRequest;
use Goodcatch\Modules\Core\Model\Admin\Schedule;
use Goodcatch\Modules\Core\Repositories\Admin\ScheduleLogRepository;
use Goodcatch\Modules\Core\Repositories\Admin\ScheduleRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    protected $formNames = ['name', 'description', 'input', 'cron',
        'ping_before', 'ping_success', 'ping_failure', 'schedule_type',
        'order', 'once', 'status', 'payload', 'overlapping', 'one_server',
        'background', 'maintenance', 'group'];


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return $this->success(
            new ScheduleCollection(ScheduleRepository::list(
                $request->per_page??30,
                $request->only($this->formNames),
                $request->keyword
            )), __('base.success'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $id number Schecule ID
     * @return \Illuminate\Http\Response
     */
    public function logs(Request $request, $id)
    {
        return $this->success(
            ScheduleLogRepository::recent(
                $request->per_page??30,
                $id,
                $request->keyword
            ), __('base.success')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->success(ScheduleRepository::find($id), __('base.success'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ScheduleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ScheduleRequest $request)
    {
        try{
            return $this->success(ScheduleRepository::add($request->only($this->formNames)), __('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据已存在' : '其它错误'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ScheduleRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update (ScheduleRequest $request, $id)
    {
        $data = $request->only ($this->formNames);
        $start = $request->start;

        try {
            $res = ScheduleRepository::update ($id, $data);
            if (! empty ($start))
            {
                $schedule = Schedule::find ($id);
                if ($schedule->schedule_type === Schedule::TYPE_JOB)
                {
                    dispatch (new $schedule->input ($schedule->payload));
                }
            }

            return $this->success($res, __('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前数据已存在' : '其它错误'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $idArray = array_filter(explode(',',$id),function($item){
            return is_numeric($item);
        });

        try{
            return $this->success(ScheduleRepository::delete($idArray), __('base.success'));
        } catch (QueryException $e) {
            return $this->error(__('base.error') . $e->getMessage());
        }
    }


}
