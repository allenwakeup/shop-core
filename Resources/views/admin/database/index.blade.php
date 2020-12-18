@extends('admin.base')

@section('content')
    @include('admin.breadcrumb')

@php
    $user = \Auth::guard('admin')->user();
    $isSuperAdmin = in_array($user->id, config('light.superAdmin'));
@endphp


<div class="layui-row">
    <div class="layui-col-md4">
        <div class="layui-card">
            <div class="layui-card-body">
                <table class="layui-table" lay-data="{url:'{{ route ('admin::' . module_route_prefix ('.') . 'core.database.list') }}', page:true, limit:10, id:'table', toolbar: '#toolbar',defaultToolbar:{}}" lay-filter="table">
                    <thead>
                    <tr>
                        <th lay-data="{field:'schema', width:150}">数据库</th>
                        <th lay-data="{field:'name'}">名称</th>
                        <th lay-data="{field:'rows', width:120}">行数</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="layui-col-md8">
        <div class="layui-card">
            <div class="layui-card-body">
                <table class="layui-hide" id="table_column" lay-filter="table_column"></table>
            </div>
        </div>
    </div>
</div>

@endsection
<script type="text/html" id="toolbar">
    <div class="layui-form-item">
        <div class="layui-input-inline">
            <input type="text" name="keyword" class="layui-input">
        </div>
        <button class="layui-btn layui-btn-sm" lay-event="search">
            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
        </button>

    </div>
</script>
<script type="text/html" id="toolbar2">
    <div class="layui-form-item">

        <div class="layui-input-inline">
            <input type="text" name="keyword2" class="layui-input">
        </div>
        <button class="layui-btn layui-btn-sm" lay-event="search">
            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
        </button>

    </div>
</script>
@section('js')
    <script>
        layui.use([ 'layer',  'table'], function() {
            var table = layui.table;

            //方法级渲染
            table.render({
                elem: '#table_column'
                ,cols: [[
                    ,{field:'Field', title: '字段', width:150}
                    ,{field:'Type', title: '字段类型', width:200}
                    ,{field:'Null', title: '是否空', width:80}
                    ,{field:'Key', title: '是否主键', width:90}
                    ,{field:'Default', title: '默认值'}
                ]]
                ,data: []
                ,id: 'table_column'
                ,page: true
                ,limit:10
                ,toolbar: '#toolbar2'
                ,defaultToolbar:{}
            });

            table.on('toolbar(table)', function(obj){
                switch(obj.event){
                    case 'search':
                        var keyword = $ ('input[name="keyword"]').val ();
                        if (keyword)
                        {
                            table.reload ('table', {
                                url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.database.list') }}'
                                ,where: {
                                    keyword: $ ('input[name="keyword"]').val ()
                                }
                                ,page: {
                                    curr: 1
                                }
                            });
                        }

                    break;

                }
            });

            table.on('toolbar(table_column)', function(obj){
                switch(obj.event){
                    case 'search':
                        var keyword = $ ('input[name="keyword2"]').val ();
                        if (keyword)
                        {
                            table.reload ('table_column', {
                                url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.database.list') }}'
                                ,where: {
                                    type: 1,
                                    keyword: $ ('input[name="keyword2"]').val ()
                                }
                                ,page: {
                                    curr: 1
                                }
                            });
                        }

                    break;

                }
            });
            //监听行双击事件
            table.on('rowDouble(table)', function (obj){

                table.reload('table_column', {
                    url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.database.list') }}'
                    ,where: {
                        table : obj.data.id,
                        type: 1
                    }
                    ,page: {
                        curr: 1
                    }
                });
                obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
            });
        });

    </script>
@endsection
