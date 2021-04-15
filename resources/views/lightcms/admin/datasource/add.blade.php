@extends('admin.base')

@section('content')
    <div class="layui-card">

        @include('admin.breadcrumb')

        <div class="layui-card-body">
            <form class="layui-form" action="@if (isset ($id)){{ route ('admin::' . module_route_prefix ('.') . 'core.datasource.update', ['id' => $id]) }}@else{{ route ('admin::' . module_route_prefix ('.') . 'core.datasource.save') }}@endif" method="post">
                @if(isset($id)) {{ method_field('PUT') }} @endif
                <div class="layui-form-item">
                    <label class="layui-form-label">代码</label>
                    <div class="layui-input-block">
                        <input type="text" name="code" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->code ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->name ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <textarea name="description" autocomplete="off" class="layui-textarea">{{ $model->description ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">必填字段</label>
                    <div class="layui-input-block">
                        <textarea name="requires" autocomplete="off" placeholder="例如 Mysql： driver,host,port,database,username,password （所有可用字段：conn_type,driver,host,port,database,username,password,url,unix_socket,charset,collation,prefix,strict,engine,schema,sslmode）" class="layui-textarea">{{ $model->requires ?? ''  }}</textarea>
                        <div class="layui-form-mid layui-color layui-word-aux">字段名之间用英文的逗号分割，不留空格</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">选填字段</label>
                    <div class="layui-input-block">
                        <textarea name="options" autocomplete="off" placeholder="例如 Mysql： unix_socket,charset,collation,prefix,strict,engine （所有可用字段：conn_type,driver,host,port,database,username,password,url,unix_socket,charset,collation,prefix,strict,engine,schema,sslmode）" class="layui-textarea">{{ $model->options ?? ''  }}</textarea>
                        <div class="layui-form-mid layui-color layui-word-aux">字段名之间用英文的逗号分割，不留空格</div>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">排序</label>
                    <div class="layui-input-inline">
                        <input type="number" name="order" autocomplete="off" class="layui-input" value="{{ $model->order ?? 1  }}">
                    </div>
                    <div class="layui-form-mid layui-word-aux">值越小排序越靠前</div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否启用</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="status" lay-skin="switch" lay-text="启用|禁用" value="1" @if(isset($model) && $model->status == Goodcatch\Modules\Core\Model\Admin\Datasource::STATUS_ENABLE) checked @endif>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formDatasource" id="submitBtn">提交</button>
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
        form.on ('submit(formDatasource)', function (data){
            window.form_submit = $ ('#submitBtn');
            form_submit.prop ('disabled', true);
            if (! data.field.status)
            {
                data.field.status = {{Goodcatch\Modules\Core\Model\Admin\Datasource::STATUS_DISABLE}};
            }
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
    </script>
@endsection
