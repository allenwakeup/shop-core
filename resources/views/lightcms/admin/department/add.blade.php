@extends('admin.base')

@section('content')
    <div class="layui-card">

        @include('admin.breadcrumb')

        <div class="layui-card-body">
            <form class="layui-form" action="@if(isset($id)){{ route('admin::' . module_route_prefix ('.') . 'core.department.update', ['id' => $id]) }}@else{{ route('admin::' . module_route_prefix ('.') . 'core.department.save') }}@endif" method="post">
                @if(isset($id)) {{ method_field('PUT') }} @endif
                    <div class="layui-form-item">
                        <label class="layui-form-label">名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->name ?? ''  }}">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">上级部门</label>
                        <div class="layui-input-block" style="width: 400px">
                            <select name="pid" lay-verify="required">
                                <option value="0">顶级部门</option>
                                @foreach(Goodcatch\Modules\Core\Repositories\Admin\DepartmentRepository::tree() as $v)
                                    @include('admin.menu', $v)
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">排序</label>
                        <div class="layui-input-block">
                            <input type="text" name="order" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->order ?? 0  }}">
                        </div>
                    </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formDepartment" id="submitBtn">提交</button>
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
        form.on('submit(formDepartment)', function(data){
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
    </script>
@endsection
