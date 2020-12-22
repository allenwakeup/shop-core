@extends('admin.base')

@section('title', '基础数据')

@section('content')

    @include('admin.breadcrumb')

    <div class="layui-row">
        @if (! empty ($schedules))
            <div class="layui-col-md4">
                @include('core::admin.widget.card', [
                    'title' => '计划任务',
                    'quick_link' => route ('admin::' . module_route_prefix ('.') . 'core.schedule.index'),
                    'display' => $schedules->count (),
                    'tip' => $schedules->map (function ($schedule) {
                        if (! empty ($schedule->logs)) {
                            return $schedule->logs->countBy (function ($count) {
                                return $count->ua;
                            });
                        }
                    })->sum ('false') . '次成功/' . $schedules->map (function ($schedule) {
                        if (! empty ($schedule->logs)) {
                            return $schedule->logs->countBy (function ($count) {
                                return $count->ua;
                            });
                        }
                    })->sum ('true') . '次失败',
                    'details' => $schedules->reduce (function ($arr, $schedule) {
                        if (! is_null ($schedule) && ! is_null ($schedule->logs))
                        {

                            $log_status = $schedule->logs->countBy (function ($log) {
                                return $log->ua;
                            })->all ();

                            $arr [] = $schedule->name . ' - ' . array_get ($log_status, 'false', 0) . '次成功/' . array_get ($log_status, 'true', 0) . '次失败';
                        }
                        return $arr;
                    }, [])
                  ])
            </div>
        @endif
        @if(!empty ($data_routes))
            <div class="layui-col-md4">
                @include('core::admin.widget.card', [
                    'title' => '数据路径',
                    'quick_link' => route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.index'),
                    'display' => $data_routes->count (),
                    'tip' => '共关联' . $data_routes->map (function ($data_route) {
                        if (! empty ($data_route->data_maps)) {
                            return $data_route->data_maps->countBy (function ($count) {
                                return $count->status;
                            });
                        }
                    })->sum ('1') . '个映射',
                    'details' => $data_routes->reduce (function ($arr, $data_route) {
                        if (! empty ($data_route->data_maps))
                        {
                            $data_route_status = $data_route->data_maps->countBy (function ($data_map) {
                                return $data_map->status;
                            })->all ();

                            $arr [] = $data_route->name . ' - 关联' . array_get ($data_route_status, '1', 0) . '个映射';
                        }
                        return $arr;
                    }, [])
                  ])
            </div>
        @endif
        @if(!empty ($users))
            <div class="layui-col-md4">
                @include('core::admin.widget.card', [
                    'title' => '用户数',
                    'display' => $users->count ()
                  ])
            </div>
        @endif
    </div>
    <div class="layui-row">
        <div class="layui-col-md3">

        </div>
    </div>
@endsection

@section('js')
    <script>
    </script>
@endsection