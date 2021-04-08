@extends('admin.base')
@if ($is_pop)
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
@endif
@section('content')
    @if (!$is_pop)
    @include('admin.breadcrumb')
    @endif

    @php
        $user = \Auth::guard('admin')->user();
        $isSuperAdmin = in_array($user->id, config('light.superAdmin'));
    @endphp
    @if(isset($mapping))

        <div class="layui-row">
            <div class="layui-col-md4">
                <div class="layui-card">
                    <div class="layui-card-body">

                        <table class="layui-table" lay-data="{url:'{{ $select }}', page:true, limit:10, toolbar: '#toolbar', defaultToolbar: {},  id:'table'}" lay-filter="table">
                            <thead>
                            <tr>
                                <th lay-data="{field:'title'}">名称</th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
            <div class="layui-col-md8">

                <div class="layui-card">
                    <div class="layui-card-body">
                <div class="layui-transfer-container {{ $mapping }}"></div>

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
        <button class="layui-btn layui-btn-sm search" lay-event="search" lay-filter="">
            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
        </button>

    </div>
</script>
@if(isset($mapping))
@section('js')
    <script>

        $ ('#input_search').keydown (function (e) {
            if (e.keyCode == 13)
            {
                $ ('.layui-btn.search').trigger ('click');
            }
        });

        layui.use(['transfer', 'layer', 'util', 'table'], function() {
            var table = layui.table, select_id = null;

            table.on('toolbar(table)', function(obj){
                switch(obj.event){
                    case 'search':
                        var keyword = $ ('input[name="keyword"]').val ();

                        table.reload ('table', {
                            where: {
                            @isset ($model)
                            @foreach (explode ('+', $model->left_tpl) as $tpl)
                                {{explode('::', $tpl, 2) [0]}}:'',
                            @endforeach
                            @endisset
                                keyword: keyword
                            }
                            ,page: {
                                curr: 1 //重新从第 1 页开始
                            }
                        });


                        break;

                }
            });



            var transfer = layui.transfer;

            let mytransfer = transfer.render({
                elem: '.layui-transfer-container.{{ $mapping }}'
                , data: []
                , value: []
                , title: ['未分配', '已分配']
                , showSearch: true
                , width: '42%'
                // , height: '80%'
                ,parseDatas: function(res){
                    return {
                        "value": res.id //数据值
                        ,"title": res.name //数据标题
                        // ,"disabled": res.disabled  //是否禁用
                        // ,"checked": res.checked //是否选中
                    }
                }
                , onchange: function (data, index) {
                    var method = ['POST', 'DELETE'] [index];

                    layer.load ();

                    $.ajax({
                        url: @json($action)[method].replace('left_id', select_id),
                        data: {
                            '_method': method,
                            'id': data.reduce ((arr, item) => {
                                arr.push (item ['value']);
                                return arr;
                            }, [])
                        },
                        success: function (result) {
                            layer.closeAll('loading');
                        },
                        error: function (err) {
                            layer.closeAll('loading');
                        }
                    });
                }
            });

            //监听行双击事件
            table.on('rowDouble(table)', function (obj){
                @isset ($model)
                select_id = obj.data.{{$model->parent_key}};
                loadTransfer (mytransfer, {{$model->id}}, select_id);
                @endisset
                obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
            });
        });

        var form = layui.form;



        //监听提交
        form.on('submit(formAdminUser)', function(data){
            window.form_submit = $('#submitBtn');
            form_submit.prop('disabled', true);
            $.ajax({
                url: data.form.action,
                data: data.field,
                success: function (result) {
                    if (result.code !== 0) {
                        form_submit.prop('disabled', false);
                        layer.msg(result.msg, {shift: 6});
                        return false;
                    }
                    layer.msg(result.msg, {icon: 1}, function () {
                        if (result.reload) {
                            location.reload();
                        }
                        if (result.redirect) {
                            location.href = '{!! url()->previous() !!}';
                        }
                    });
                }
            });

            return false;
        });

        function loadTransfer (transfer, data_map_id, left_id)
        {
            layer.load ();


            $.ajax ({
                headers: {},
                url: '{{ route ('admin::' . module_route_prefix ('.') . 'core.dataMap.assignment.select', ['id' => $id, 'left_id' => 'left_id']) }}'.replace ('left_id', left_id),
                type: 'get',
                success: function (result) {
                    layer.closeAll('loading');

                    if (result.code !== 0) {

                        layer.msg (result.msg, {shift: 6});
                        return false;
                    }
;
                    transfer.reload ({
                        data: result.data
                        ,
                        value: result.right
                    });

                },
                error: function (resp, stat, text) {
                    layer.closeAll('loading');
                },
            });



        }

    </script>
@endsection
@endif
