<?php

namespace Goodcatch\Modules\Core\Http\Controllers\Admin;


use Goodcatch\Modules\Core\Model\Admin\DataRoute;
use Goodcatch\Modules\Core\Model\Admin\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;

class CoreController extends Controller
{

    public function __construct ()
    {
        parent::__construct ();

        $this->breadcrumb[] = ['title' => '基础数据', 'url' => route ('admin::' . module_route_prefix ('.') . 'core.index')];
    }

    /**
     * 模块-基础数据
     *
     * @return Response
     */
    public function index ()
    {
        $user = \Auth::guard ('admin')->user ();
        $isSuperAdmin = in_array ($user->id, config ('light.superAdmin'));

        $can_access_to_schedule = $can_access_to_data_route = false;

        module_tap (
            'modules.service.permission',
            function ($permission_service) use (&$can_access_to_schedule, &$can_access_to_data_route)
            {
                $can_access_to_schedule = ! is_null ($permission_service->find ('admin::' . module_route_prefix ('.') . 'core.schedule.list'));
                $can_access_to_data_route = ! is_null ($permission_service->find ('admin::' . module_route_prefix ('.') . 'core.dataRoute.list'));
            });


        if ($isSuperAdmin || $can_access_to_schedule) {
            $schedules = Schedule::ofEnabled ()->ofJob ()->ofOrdered ()->with (['logs' => function ($query)
            {
                $query->where ('type', 1)->whereDate ('created_at', Carbon::now ())->orderBy ('created_at', 'desc');
            }])->get ();
        }

        if ($isSuperAdmin || $can_access_to_data_route) {
            $data_routes = DataRoute::query ()->with (['data_maps'])->get ();
        }

        $breadcrumb = $this->breadcrumb;

        return view ('core::admin.home.index', compact (['schedules', 'data_routes', 'breadcrumb']));
    }

}
