@extends('admin.base')

@section('css')
    <style>
        .layui-layout-admin .layui-header {
            display:none;
        }
        .layui-layout-admin .layui-side {
            display:none;
        }
        .layui-layout-admin .layui-footer {
            display:none;
        }
        .layui-layout-admin .layui-body {
            top: 0;
            left: 0;
            bottom: 0;
        }
        .layui-table-tool-temp {
            padding: 0;
        }

    </style>
@endsection

@section('content')
    @php
        $user = \Auth::guard('admin')->user();
        $isSuperAdmin = in_array($user->id, config('light.superAdmin'));
    @endphp

    @if (isset ($model))
        <div class="layui-row">
            <div class="layui-col-md4">
                <div class="layui-card">
                    <div class="layui-card-body">
                        <table class="layui-table" lay-data="{url:'{{ route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.detail', ['id' => $model->id]) }}?type=from', page:true, limit:10, toolbar: '#toolbar', defaultToolbar: {}, id:'table'}" lay-filter="table">
                            <thead>
                            <tr>
                                <th lay-data="{field:'name'}">名称</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="layui-col-md8">
                <div class="layui-card">

                    <div class="layui-card-body">
                        <div class="tree-to"></div>
                    </div>
                </div>
            </div>

        </div>

    @else
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="layui-field-box">未授权数据，请联系管理员</div>
            </div>
        </div>
    @endif



@endsection
<script type="text/html" id="toolbar">
    <div class="layui-form-item" style="margin-bottom: 0;">
        <label class="layui-form-label" style="padding: 5px 15px;">搜索</label>
        <div class="layui-input-inline">
            <input type="text" name="keyword" class="layui-input" id="input_search" style="height: 30px">
        </div>
        <button class="layui-btn layui-btn-sm search" lay-event="search">
            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
        </button>
    </div>
</script>
@section('js')
    <script>

        $ ('#input_search').keydown (function (e) {
            if (e.keyCode == 13)
            {
                $ ('.layui-btn.search').trigger ('click');
            }
        });

        layui.use(['layer', 'util', 'table', 'tree'], function() {
            var table = layui.table;

            table.on('toolbar(table)', function(obj){
                switch(obj.event){
                    case 'search':
                        var keyword = $ ('input[name="keyword"]').val ();
                        table.reload ('table', {
                            url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.detail', ['id' => $model->id]) }}'
                            ,where: {
                                type: 'from'
                                ,keyword: keyword
                            }
                            ,page: {
                                curr: 1
                            }
                        });
                        break;

                }
            });

            var tree = layui.tree;

            tree.render({
                elem: '.tree-to'
                ,showCheckbox: false  //是否显示复选框
                ,showLine: true  //是否开启连接线
                ,onlyIconControl: true  //是否仅允许节点左侧图标控制展开收缩
                ,id: 'tree_to'
                ,isJump: false //是否允许点击节点时弹出新窗口跳转
                ,click: function(obj){
                    var data = obj.data;  //获取当前点击的节点数据
                    var content = [];
                        Object.keys (data).forEach (function (key){
                            if (typeof data [key] === 'string')
                            {
                                content.push ('<p style="text-align:left; font-weight: bold;">' + data[key] + '</p>');
                            }
                        });
                    layer.msg(content.join (''), {
                        time: 10 * 1000, // 自动关闭
                        btn: ['知道了']
                    });
                }
            });



            //监听行双击事件
            table.on('row(table)', function (obj){

                layer.load();

                $.ajax({
                    url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.detail', ['id' => $model->id]) }}',
                    type: 'get',
                    data: {
                        type: 'to',
                        @if (isset ($left_data_map))
                        left_id: obj.data.{{ $left_data_map->parent_key }}
                        @else
                        left_id: obj.data.id
                        @endif
                    },
                    success: function (data) {
                        layer.closeAll('loading');
                        tree.reload ('tree_to', {data:data.data});
                    },
                    error: function (err) {
                        layer.closeAll('loading');
                    }
                });

                obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
            });
        });

    </script>
@endsection
