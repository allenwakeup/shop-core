@extends('admin.base')

@section('content')
    @include('admin.breadcrumb')

    <div class="layui-card">
        <div class="layui-form layui-card-header light-search" style="height: auto">
            <form>
                <input type="hidden" name="action" value="search">
            @include ('admin.searchField', ['data' => Goodcatch\Modules\Core\Model\Admin\Schedule::$searchField])
            <div class="layui-inline">
                <label class="layui-form-label">创建日期</label>
                <div class="layui-input-inline">
                    <input type="text" name="created_at" class="layui-input" id="created_at" value="{{ request ()->get ('created_at') }}">
                </div>
            </div>
            <div class="layui-inline">
                <button class="layui-btn layuiadmin-btn-list" lay-filter="form-search" id="submitBtn">
                    <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                </button>
            </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table class="layui-table" lay-data="{url:'{{ route ('admin::' . module_route_prefix ('.') . 'core.schedule.list') }}?{{ request ()->getQueryString () }}', page:true, limit:50, id:'table', toolbar:'<div><a href=\'{{ route ('admin::' . module_route_prefix ('.') . 'core.schedule.create') }}\'><i class=\'layui-icon layui-icon-add-1\'></i>新增计划任务</a>&nbsp;&nbsp;&nbsp;&nbsp;@foreach($groups as $v)<a class=\'layui-btn layui-btn-normal layui-btn-sm\' href=\'{{ route ('admin::' . module_route_prefix ('.') . 'core.schedule.index') }}?group={{ $v }}\'>{{ empty ($v) ? '未分组' : $v }}</a>@endforeach</div>'}" lay-filter="table">
                <thead>
                <tr>
                    <th lay-data="{field:'id', width:80, sort: true, event: 'detail', style:'cursor: pointer;', templet:'#id'}">ID</th>
                    @include ('admin.listHead', ['data' => Goodcatch\Modules\Core\Model\Admin\Schedule::$listField])
                    <th lay-data="{field:'created_at'}">添加时间</th>
                    <th lay-data="{field:'updated_at'}">更新时间</th>
                    <th lay-data="{width:200, templet:'#action'}">操作</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
<script type="text/html" id="id">
    <%# if(d.logs && d.logs.length > 0) { %>
    <a href="javascript:;" class="layui-table-link" style="font-weight:bold;"><% d.id %></a>
    <%# } else { %>
    <% d.id %>
    <%# } %>
</script>
<script type="text/html" id="action">
    <a href="<% d.editUrl %>" class="layui-table-link" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>
    <a href="javascript:;" class="layui-table-link" title="删除" style="margin-left: 15px" onclick="deleteSchedule ('<% d.deleteUrl %>')"><i class="layui-icon layui-icon-delete"></i></a>
</script>
<script type="text/html" id="name">
    <%# if(d.schedule_type === {{ \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB }}) { %>
    <input type="hidden" name="start" value="true">
    <a href="javascript:;" class="layui-table-link" title="执行" lay-event="nameEvent"><i class="layui-icon layui-icon-play" style="font-weight: bold"></i></a>
    <%# } %>
    <% d.name %>
</script>
<script type="text/html" id="onceText">
    <%# if(d.once === 1) { %>
    <span class="layui-badge layui-bg-orange">单次</span>
    <%# } else { %>
    <span class="layui-badge layui-bg-green">可循环</span>
    <%# } %>
</script>
<script type="text/html" id="overlappingText">
    <%# if(d.overlapping === 1) { %>
    <span class="layui-badge layui-bg-green">不可重复</span>
    <%# } else { %>
    <span class="layui-badge layui-bg-orange">可重复</span>
    <%# } %>
</script>
<script type="text/html" id="oneServerText">
    <%# if(d.one_server === 1) { %>
    <span class="layui-badge layui-bg-green">单服务器</span>
    <%# } else { %>
    <span class="layui-badge layui-bg-orange">多服务器</span>
    <%# } %>
</script>
<script type="text/html" id="backgroundText">
    <%# if(d.background === 1) { %>
    <span class="layui-badge layui-bg-orange">后台执行</span>
    <%# } else { %>
    <span class="layui-badge layui-bg-green">前台执行</span>
    <%# } %>
</script>
<script type="text/html" id="maintenanceText">
    <%# if(d.maintenance === 1) { %>
    <span class="layui-badge layui-bg-orange">执行</span>
    <%# } else { %>
    <span class="layui-badge layui-bg-green">不执行</span>
    <%# } %>
</script>

@include('core::admin.listHeadTpl', ['data' => Goodcatch\Modules\Core\Model\Admin\Schedule::$listField])
@section('js')
    <script>
        var laytpl = layui.laytpl;
        laytpl.config ({
            open: '<%',
            close: '%>'
        });

        var laydate = layui.laydate;
        laydate.render ({
            elem: '#created_at',
            range: '~'
        });

        function deleteSchedule (url) {
            layer.confirm ('确定删除？', function (index){
                $.ajax({
                    url: url,
                    data: {'_method': 'DELETE'},
                    success: function (result) {
                        if (result.code !== 0) {
                            layer.msg(result.msg, {shift: 6});
                            return false;
                        }
                        layer.msg (result.msg, {icon: 1}, function () {
                            if (result.reload) {
                                location.reload ();
                            }
                            if (result.redirect) {
                                location.href = '{!! url ()->previous () !!}';
                            }
                        });
                    }
                });

                layer.close (index);
            });
        }

        layui.use('table', function() {
            var table = layui.table;
            table.on('tool(table)', function(obj){
                var data = obj.data;
                if(obj.event === 'detail'){
                    showDetail(data.detailUrl, '任务详情', '50%', '50%')
                } else {
                    var event = obj.event, tr = obj.tr;

                    var maps = {
                        statusEvent: "status",
                        nameEvent: "start"
                    };
                    var key = maps[event];
                    var val = {
                        "status":tr.find("input[name='" + key + "']").prop('checked') ? {{ Goodcatch\Modules\Core\Model\Admin\Schedule::STATUS_ENABLE }} : {{ Goodcatch\Modules\Core\Model\Admin\Schedule::STATUS_DISABLE }},
                        "start":tr.find("input[name='" + key + "']").val()
                    } [key];
                    let data = Object.assign (obj.data, {id: obj.data.id, '_method': 'PUT'});

                    data [key] = val;
                    delete data.logs;

                    if (data.payload)
                    {
                        data.payload = JSON.stringify (data.payload);
                    }

                    layer.load ();

                    $.ajax({
                        url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.schedule.update', ['id' => '_replace_id_']) }}'.replace('_replace_id_', obj.data.id),
                        method: 'put',
                        dataType: 'json',
                        data: data,
                        success: function (result) {
                            layer.closeAll('loading');
                            if (result.code !== 0) {
                                layer.msg(result.msg, {shift: 3});
                                return false;
                            }
                            layer.msg(result.msg, {icon: 1});
                            // location.reload();
                        },
                        error: function (err) {
                            layer.closeAll('loading');
                        }
                    });
                }
            });
            table.on('rowDouble(table)', function (obj) {
                var data = obj.data;

                window.location.href = data.editUrl;

            });
        });
    </script>
@endsection
