@extends('admin.base')

@section('content')
    <div class="layui-card">

        @include('admin.breadcrumb')

        <div class="layui-card-body">
            <form class="layui-form" action="@if (isset ($id)){{ route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.update', ['id' => $id]) }}@else{{ route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.save') }}@endif" method="post">
                @if(isset($id)) {{ method_field('PUT') }} @endif
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">路径名称</label>
                        <div class="layui-input-block" style="width: 300px;">
                            <input type="text" name="name" required  lay-verify="required" placeholder="请输入路径名称" autocomplete="off" class="layui-input" value="{{ $model->name ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">路径简称</label>
                        <div class="layui-input-block">
                            <input type="text" name="short" autocomplete="off" class="layui-input" value="{{ $model->short ?? ''  }}">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">路径别名</label>
                    <div class="layui-input-block">
                        <input type="text" name="alias" autocomplete="off" class="layui-input" value="{{ $model->alias ?? ''  }}">
                        <div class="layui-form-mid layui-color layui-word-aux"><span class="layui-badge">注：</span>用作自动菜单分组名</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">头表名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="from" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->from ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">头表</label>
                        <div class="layui-input-block" style="width: 320px;">
                            <div class="xm-select-table-from"></div>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-form-mid layui-color layui-word-aux"><span class="layui-badge">注：</span>表不可更改</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">尾表名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="to" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->to ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">尾表</label>
                        <div class="layui-input-block" style="width: 320px;">
                            <div class="xm-select-table-to"></div>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-form-mid layui-color layui-word-aux"><span class="layui-badge">注：</span>表不可更改</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">输出表</label>
                        <div class="layui-input-block">
                            <input type="text" name="output" autocomplete="off" class="layui-input" value="{{ $model->outputOrigin ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">使用连接</label>
                        <div class="layui-input-block" style="width: 320px;">
                            <div class="xm-select-connection"></div>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-form-mid layui-color layui-word-aux"><span class="layui-badge">注：</span>可选项</div>
                    </div>
                    @if(isset ($id))
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-color layui-word-aux">
                            虽然可以修改输出表的表名，但要注意的是，修改前的输出表「 {{ $model->output }} 」并未作任何处理。
                        </div>
                    </div>
                    @else
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-color layui-word-aux">
                            输出表的表名将自动加入前缀 「 {{ light_config ('DATA_EXCHANGE_DATA_ROUTE_PREFIX', 'sync_') }} 」
                        </div>
                    </div>
                    @endif
                </div>


                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <textarea name="description" autocomplete="off" placeholder="这个数据路径是用来作什么用途的？什么都没写" class="layui-textarea">{{ $model->description ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formDataRoute" id="submitBtn">提交</button>
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
        form.on ('submit(formDataRoute)', function (data){
            window.form_submit = $ ('#submitBtn');
            form_submit.prop ('disabled', true);
            $.ajax ({
                url: data.form.action,
                data: data.field,
                success: function (result) {
                    if (result.code !== 0) {
                        form_submit.prop ('disabled', false);
                        layer.msg (result.msg, {shift: 6});
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

            return false;
        });


        xmSelect.render ({
            el: '.layui-form .layui-input-block .xm-select-table-from',
            prop: {
                name: 'name',
                value: 'id',
            },
            @if(isset($id)) disabled: true, @endif
            radio: true,
            name: 'table_from',
            autoRow: true,
            clickClose: true,
            delay: 500,
            filterable: true,
            remoteSearch: true,
            remoteMethod: function (val, cb, show) {
                $.ajax({
                    url: '{{ route('admin::' . module_route_prefix ('.') . 'core.database.list') }}?limit=99999&keyword=' + val,
                    type: 'GET',
                    success: function (result) {
                        @if(isset ($id) && isset ($model->table_from))
                        result.data.forEach ((d, k)=>{ if ((d.id + '') === '{{ $model->table_from }}') d.selected = true; });
                        @endif
                        cb (result.data);
                    }
                });
            }
        });

        xmSelect.render ({
            el: '.layui-form .layui-input-block .xm-select-table-to',
            prop: {
                name: 'name',
                value: 'id',
            },
            @if(isset($id)) disabled: true, @endif
            radio: true,
            name: 'table_to',
            autoRow: true,
            clickClose: true,
            delay: 500,
            filterable: true,
            remoteSearch: true,
            remoteMethod: function (val, cb, show) {
                $.ajax({
                    url: '{{ route('admin::' . module_route_prefix ('.') . 'core.database.list') }}?limit=99999&keyword=' + val,
                    type: 'GET',
                    success: function (result) {
                        @if(isset ($id) && isset ($model->table_to))
                        result.data.forEach ((d, k)=>{ if ((d.id + '') === '{{ $model->table_to }}') d.selected = true; });
                        @endif
                        cb (result.data);
                    }
                });
            }
        });



        let has_connection_xm = false;
        xmSelect.render ({
            el: '.layui-form .layui-input-block .xm-select-connection',
            prop: {
                name: 'name',
                value: 'id',
            },
            radio: true,
            name: 'connection_id',
            autoRow: true,
            clickClose: true,
            delay: 500,
            filterable: true,
            remoteSearch: true,
            remoteMethod: function (val, cb, show) {
                $.ajax({
                    url: '{{ route('admin::' . module_route_prefix ('.') . 'core.connection.list') }}?limit=99999&keyword=' + val,
                    type: 'GET',
                    success: function (result) {

                        if (! has_connection_xm)
                        {
                            has_connection_xm = (result.data && result.data.length > 0);
                        }

                        if (has_connection_xm)
                        {
                            @if(isset ($id) && isset ($model->connection_id))
                            result.data.forEach ((d, k)=>{ if ((d.id + '') === '{{ $model->connection_id }}') d.selected = true; });
                            @endif
                            cb (result.data);
                        } else {
                            $ ('.layui-input-block .xm-select-connection')
                                .hide ()
                                .parent ()
                                .append ('<div class="layui-input layui-bg-gray" style="line-height: 35px;"><a class="layui-table-link" href="{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.create') }}">新增一个数据连接</a></div>')
                            ;
                        }
                    }
                });
            }
        });

    </script>
@endsection
