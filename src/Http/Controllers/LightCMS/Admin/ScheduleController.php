<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\LightCMS\Admin;

use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Http\Requests\Admin\ScheduleRequest;
use Goodcatch\Modules\Core\Model\Admin\Schedule;
use Goodcatch\Modules\Core\Repositories\Admin\ScheduleRepository;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    protected $formNames = ['name', 'description', 'input', 'cron',
        'ping_before', 'ping_success', 'ping_failure', 'schedule_type',
        'order', 'once', 'status', 'payload', 'overlapping', 'one_server',
        'background', 'maintenance', 'group'];

    public function __construct ()
    {
        parent::__construct ();

        $this->breadcrumb [] = ['title' => '计划任务列表', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.schedule.index')];
    }

    /**
     * 计划任务管理-计划任务列表
     *
     */
    public function index ()
    {
        $this->breadcrumb [] = ['title' => '计划任务列表', 'url' => ''];
        return view ('core::admin.schedule.index', ['breadcrumb' => $this->breadcrumb, 'groups' => ScheduleRepository::groupNames ()]);
    }

    /**
     * 计划任务管理-计划任务列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function list (Request $request)
    {
        $perPage = (int) $request->get ('limit', 50);
        $condition = $request->only ($this->formNames);

        $data = ScheduleRepository::list ($perPage, $condition, $request->keyword);

        return $data;
    }

    /**
     * 计划任务管理-新增计划任务
     *
     */
    public function create ()
    {
        $this->breadcrumb [] = ['title' => '新增计划任务', 'url' => ''];
        return view ('core::admin.schedule.add', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 计划任务管理-计划任务详情
     *
     */
    public function detail($id)
    {
        $schedule = Schedule::query()->with (['logs' => function ($query) {
            $query
                ->where('type', 1)
                ->whereDate ('created_at', '>=', Carbon::yesterday ())
                ->orderBy ('created_at', 'desc')
            ;
        }])->firstWhere('id', $id);
        return view('core::admin.schedule.detail', ['model' => $schedule]);
    }

    /**
     * 计划任务管理-保存计划任务
     *
     * @param ScheduleRequest $request
     * @return array
     */
    public function save (ScheduleRequest $request)
    {
        try {
            ScheduleRepository::add ($request->only ($this->formNames));
            return [
                'code' => 0,
                'msg' => '新增成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '新增失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前计划任务已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 计划任务管理-编辑计划任务
     *
     * @param int $id
     * @return View
     */
    public function edit ($id)
    {
        $this->breadcrumb [] = ['title' => '编辑计划任务', 'url' => ''];

        $model = ScheduleRepository::find ($id);
        return view ('core::admin.schedule.add', ['id' => $id, 'model' => $model, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 计划任务管理-更新计划任务
     *
     * @param ScheduleRequest $request
     * @param int $id
     * @return array
     */
    public function update (ScheduleRequest $request, $id)
    {
        $data = $request->only ($this->formNames);
        $start = $request->start;

        try {
            ScheduleRepository::update ($id, $data);
            if (! empty ($start))
            {
                $schedule = Schedule::find ($id);
                if ($schedule->schedule_type === Schedule::TYPE_JOB)
                {
                    dispatch (new $schedule->input ($schedule->payload));
                }
            }

            return [
                'code' => 0,
                'msg' => '编辑成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '编辑失败：' . (Str::contains ($e->getMessage (), 'Duplicate entry') ? '当前计划任务已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 计划任务管理-删除计划任务
     *
     * @param int $id
     * @return array
     */
    public function delete ($id)
    {
        try {
            ScheduleRepository::delete ($id);
            return [
                'code' => 0,
                'msg' => '删除成功',
                'redirect' => route ('admin::' . module_route_prefix ('.') . 'core.schedule.index')
            ];
        } catch (\RuntimeException $e) {
            return [
                'code' => 1,
                'msg' => '删除失败：' . $e->getMessage (),
                'redirect' => false
            ];
        }
    }

}
