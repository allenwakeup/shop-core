@extends('admin.base')

@section('content')
    @include('admin.breadcrumb')

    <div class="layui-card">
        <div class="layui-form layui-card-header light-search" style="height: auto">
            <form>
                <input type="hidden" name="action" value="search">
            @include ('core::admin.searchField', ['data' => Goodcatch\Modules\Core\Model\Admin\Connection::$searchField])
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

            <div class="layui-tab layui-tab-card">
                <ul class="layui-tab-title">
                    <li lay-id="1" class="layui-this">@lang('core::pages.admin.connection.list.tab.all')</li>
                    <li lay-id="2">@lang('core::pages.admin.connection.list.tab.source')</li>
                    <li lay-id="3">@lang('core::pages.admin.connection.list.tab.target')</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <table class="layui-table" lay-filter="table" lay-data="{url:'{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.list') }}?{{ request ()->getQueryString () }}', page:true, limit:50, id:'test', toolbar:'<div><a href=\'{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.create') }}\'><i class=\'layui-icon layui-icon-add-1\'></i>新增连接</a></div>'}">
                            <thead>
                            <tr>
                                <th lay-data="{field:'id', width:80, sort: true, event: 'detail', style:'cursor: pointer;'}">ID</th>
                                @include('admin.listHead', ['data' => Goodcatch\Modules\Core\Model\Admin\Connection::$listField])
                                <th lay-data="{field:'created_at'}">添加时间</th>
                                <th lay-data="{field:'updated_at'}">更新时间</th>
                                <th lay-data="{width:200, templet:'#action'}">操作</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="layui-tab-item">
                        <table class="layui-table" lay-filter="table{{ Goodcatch\Modules\Core\Model\Admin\Connection::TYPE_SRC }}" lay-data="{url:'{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.list') }}?type={{ Goodcatch\Modules\Core\Model\Admin\Connection::TYPE_SRC }}&{{ request ()->getQueryString () }}', page:true, limit:50, id:'test{{ Goodcatch\Modules\Core\Model\Admin\Connection::TYPE_SRC }}', toolbar:'<div><a href=\'{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.create') }}\'><i class=\'layui-icon layui-icon-add-1\'></i>新增连接</a></div>'}">
                            <thead>
                            <tr>
                                <th lay-data="{field:'id', width:80, sort: true, event: 'detail', style:'cursor: pointer;'}">ID</th>
                                @include('admin.listHead', ['data' => Goodcatch\Modules\Core\Model\Admin\Connection::$listField])
                                <th lay-data="{field:'created_at'}">添加时间</th>
                                <th lay-data="{field:'updated_at'}">更新时间</th>
                                <th lay-data="{width:200, templet:'#action'}">操作</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="layui-tab-item">
                        <table class="layui-table" lay-filter="table{{ Goodcatch\Modules\Core\Model\Admin\Connection::TYPE_DST }}" lay-data="{url:'{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.list') }}?type={{ Goodcatch\Modules\Core\Model\Admin\Connection::TYPE_DST }}&{{ request ()->getQueryString () }}', page:true, limit:50, id:'test{{ Goodcatch\Modules\Core\Model\Admin\Connection::TYPE_DST }}', toolbar:'<div><a href=\'{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.create') }}\'><i class=\'layui-icon layui-icon-add-1\'></i>新增连接</a></div>'}">
                            <thead>
                            <tr>
                                <th lay-data="{field:'id', width:80, sort: true, event: 'detail', style:'cursor: pointer;'}">ID</th>
                                @include('admin.listHead', ['data' => Goodcatch\Modules\Core\Model\Admin\Connection::$listField])
                                <th lay-data="{field:'created_at'}">添加时间</th>
                                <th lay-data="{field:'updated_at'}">更新时间</th>
                                <th lay-data="{width:200, templet:'#action'}">操作</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
<script type="text/html" id="action">
    <a href="<% d.editUrl %>" class="layui-table-link" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>
    <a href="javascript:;" class="layui-table-link" title="删除" style="margin-left: 10px" onclick="deleteConnection ('<% d.deleteUrl %>')"><i class="layui-icon layui-icon-delete"></i></a>
</script>
<script type="text/html" id="name">
    <%# if(d.datasource) { %>
    「<% d.datasource.name %> 」<% d.name %>
        <%# if(d.host) { %>
        @<% d.host%>
        <%# } %>
    <%# } else { %>
        <% d.name %>
    <%# } %>
</script>
@include('core::admin.listHeadTpl', ['data' => Goodcatch\Modules\Core\Model\Admin\Connection::$listField])
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

        function deleteConnection (url) {
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
                                // location.reload ();
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

            let tool = function(obj){


                if(obj.event === 'detail'){
                    showDetail(data.detailUrl, '连接详情');
                } else {
                    var event = obj.event, tr = obj.tr;

                    var maps = {
                        statusEvent: "status",
                    };
                    var key = maps[event];
                    var val = tr.find("input[name='" + key + "']").prop('checked') ? {{ Goodcatch\Modules\Core\Model\Admin\Connection::STATUS_ENABLE }} : {{ Goodcatch\Modules\Core\Model\Admin\Connection::STATUS_DISABLE }};

                    let data = Object.assign (obj.data, {id: obj.data.id, '_method': 'PUT'});

                    data [key] = val;

                    delete data.datasource;

                    layer.load();

                    $.ajax({
                        url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.update', ['id' => '_replace_id_']) }}'.replace ('_replace_id_', obj.data.id),
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
                            location.reload();
                        },
                        error: function (err) {
                            layer.closeAll('loading');
                        }
                    });
                }

            };

            table.on('tool(table)', tool);
            table.on('tool(table{{ Goodcatch\Modules\Core\Model\Admin\Connection::TYPE_SRC }})', tool);
            table.on('tool(table{{ Goodcatch\Modules\Core\Model\Admin\Connection::TYPE_DST }})', tool);


            table.on('rowDouble(table)', function (obj) {
                var data = obj.data;

                window.location.href = data.editUrl;

            });
        });

    </script>
@endsection
