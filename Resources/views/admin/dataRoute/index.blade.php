@extends('admin.base')

@section('content')
    @include('admin.breadcrumb')

    <div class="layui-card">
        <div class="layui-form layui-card-header light-search" style="height: auto">
            <form>
                <input type="hidden" name="action" value="search">
            @include ('admin.searchField', ['data' => Goodcatch\Modules\Core\Model\Admin\DataRoute::$searchField])
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
            <table class="layui-table" lay-data="{url:'{{ route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.list') }}?{{ request ()->getQueryString () }}', page:true, limit:50, id:'test', toolbar:'<div><a href=\'{{ route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.create') }}\'><i class=\'layui-icon layui-icon-add-1\'></i>新增数据路径</a></div>'}" lay-filter="table">
                <thead>
                <tr>
                    <th lay-data="{field:'id', width:80, sort: true, event: 'detail', style:'cursor: pointer;'}">ID</th>
                    @include ('admin.listHead', ['data' => Goodcatch\Modules\Core\Model\Admin\DataRoute::$listField])
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
    <a href="javascript:;" class="layui-table-link" title="删除" style="margin-left: 10px" onclick="deleteDataRoute ('<% d.deleteUrl %>')"><i class="layui-icon layui-icon-delete"></i></a>
</script>
@include('core::admin.listHeadTpl', ['data' => Goodcatch\Modules\Core\Model\Admin\DataRoute::$listField])
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

        function deleteDataRoute (url) {
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
                    showAssignment (data.detailUrl, '数据路径详情', '80%')
                }
            });
            table.on ('rowDouble(table)', function (obj) {
                var data = obj.data;

                window.location.href = data.editUrl;

            });
        });
    </script>
@endsection
