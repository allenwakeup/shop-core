@extends('admin.base')

@section('content')
    @include('admin.breadcrumb')

    <div class="layui-card">
        <div class="layui-form layui-card-header light-search" style="height: auto">
            <form>
                <input type="hidden" name="action" value="search">
            @include ('admin.searchField', ['data' => Goodcatch\Modules\Core\Model\Admin\DataMap::$searchField])
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
            <table class="layui-table" lay-data="{url:'{{ route ('admin::' . module_route_prefix ('.') . 'core.dataMap.list') }}?{{ request ()->getQueryString () }}', page:true, limit:50, id:'test', toolbar:'<div><a href=\'{{ route ('admin::' . module_route_prefix ('.') . 'core.dataMap.create') }}\'><i class=\'layui-icon layui-icon-add-1\'></i>新增数据映射</a></div>'}" lay-filter="table">
                <thead>
                <tr>
                    <th lay-data="{field:'id', width:80, sort: true, event: 'detail', style:'cursor: pointer;'}">ID</th>
                    <th lay-data="{field:'data_route', width:200, templet:'#dataRouteText'}">数据路径</th>
                    @include ('admin.listHead', ['data' => Goodcatch\Modules\Core\Model\Admin\DataMap::$listField])
                    <th lay-data="{field:'created_at'}">添加时间</th>
                    <th lay-data="{field:'updated_at'}">更新时间</th>
                    <th lay-data="{width:200, templet:'#action'}">操作</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
<script type="text/html" id="action">
    <a href="<% d.editUrl %>" class="layui-table-link" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>
    <a href="javascript:;" onclick="showAssignment('<% d.assignmentUrl %>', '<% d.left + d.right %>映射', '80%', '70%')" class="layui-table-link" style="margin-left: 10px"  title="<% d.left + d.right %>映射"><i class="layui-icon layui-icon-auz"></i></a>
    <a href="javascript:;" class="layui-table-link" title="删除" style="margin-left: 10px" onclick="deleteDataMap ('<% d.deleteUrl %>')"><i class="layui-icon layui-icon-delete"></i></a>
</script>
<script type="text/html" id="dataRouteText">
    <%# if(d.data_route) { %>
    <% d.data_route.name %>
    <%# } %>
</script>
@include('core::admin.listHeadTpl', ['data' => Goodcatch\Modules\Core\Model\Admin\DataMap::$listField])

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

        function deleteDataMap (url) {
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

        layui.use ('table', function () {
            var table = layui.table;
            table.on ('tool(table)', function (obj){
                var data = obj.data;
                if(obj.event === 'detail') {
                    showDetail (data.detailUrl, '数据映射详情')
                } else {
                    var event = obj.event, tr = obj.tr;

                    var maps = {
                        statusEvent: "status",
                    };
                    var key = maps[event];
                    var val = tr.find("input[name='" + key + "']").prop('checked') ? {{ Goodcatch\Modules\Core\Model\Admin\DataMap::STATUS_ENABLE }} : {{ Goodcatch\Modules\Core\Model\Admin\DataMap::STATUS_DISABLE }};

                    let data = Object.assign (obj.data, {id: obj.data.id, '_method': 'PUT'});

                    data [key] = val;

                    if (data ['payload'])
                    {
                        data ['payload'] = JSON.stringify (data ['payload']);
                    }

                    layer.load();

                    $.ajax({
                        url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.dataMap.update', ['id' => '_replace_id_']) }}'.replace('_replace_id_', obj.data.id),
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
            table.on ('rowDouble(table)', function (obj) {
                var data = obj.data;

                window.location.href = data.editUrl;

            });
        });


    </script>
@endsection
