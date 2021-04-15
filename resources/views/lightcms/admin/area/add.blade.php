@extends('admin.base')

@section('content')
    <div class="layui-card">

        @include('admin.breadcrumb')

        <div class="layui-card-body">
            <form class="layui-form" action="@if(isset($id)){{ route('admin::' . module_route_prefix ('.') . 'core.area.update', ['id' => $id]) }}@else{{ route('admin::' . module_route_prefix ('.') . 'core.area.save') }}@endif" method="post">
                @if(isset($id)) {{ method_field('PUT') }} @endif
                <div class="layui-form-item">
                    <label class="layui-form-label">省/市/县</label>
                    <div class="layui-input-inline">
                        <div class="xm-select-code"></div>
                    </div>
                    <div class="layui-form-mid layui-word-aux">数据量较大，点击第1次拉取数据，点击第2次进入下一级</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->name ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">简称</label>
                    <div class="layui-input-block">
                        <input type="text" name="short" autocomplete="off" class="layui-input" value="{{ $model->short ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">显示</label>
                    <div class="layui-input-inline">
                        <input type="text" name="display" autocomplete="off" class="layui-input" value="{{ $model->display ?? ''  }}">
                    </div>
                    <div class="layui-form-mid layui-word-aux">设备显示名称，最大50个字符</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">别名</label>
                    <div class="layui-input-block">
                        <input type="text" name="alias" autocomplete="off" class="layui-input" value="{{ $model->alias ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <textarea name="description" autocomplete="off" class="layui-textarea">{{ $model->description ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formAdminUser" id="submitBtn">提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
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


        (function () {

            $.ajax({
                url: '{{ route('admin::' . module_route_prefix ('.') . 'core.area.list') }}?limit=99999&select=province',
                type: 'GET',
                success: function (result) {
                    let remote_data = result.data;
                    let code_xm = xmSelect.render({
                        el: '.layui-form .layui-input-inline .xm-select-code',
                        name: 'code',
                        radio: true,
                        autoRow: true,
                        data: remote_data,
                        cascader: {
                            show: true,
                            indent: 320,
                            strict: true,
                        },
                        on: function (data){
                            if (data.arr.length > 0)
                            {
                                let selected = data.arr [0];
                                let next = {
                                    province: 'city',
                                    city: 'county'
                                } [selected.cascader];
                                if (selected.children && selected.children.length == 0 && next)
                                {
                                    let req_data = {
                                        limit: 99999
                                        , select: next
                                    };
                                    req_data [selected.cascader + "_id"] = selected.value;
                                    $.ajax({
                                        url: '{{ route('admin::' . module_route_prefix ('.') . 'core.area.list') }}',
                                        data: req_data,
                                        type: 'GET',
                                        success: function (next_result) {
                                            selected.children = next_result.data;
                                            code_xm.update ({ data: remote_data });
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            });
        }) ();

    </script>
@endsection
