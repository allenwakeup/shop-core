@extends('admin.base')
@section('css')
    <style>
        .layui-form-item .layui-form-label{
            width: auto;
        }
        .layui-form-item .layui-input-block{
            margin-left: 180px;
            width: 60%;
        }
    </style>
@endsection
@section('content')
    <div class="layui-card">

        @include('admin.breadcrumb')

        <div class="layui-card-body">
            <form class="layui-form" action="@if (isset ($id)){{ route ('admin::' . module_route_prefix ('.') . 'core.dataMap.update', ['id' => $id]) }}@else{{ route ('admin::' . module_route_prefix ('.') . 'core.dataMap.save') }}@endif" method="post">
                @if(isset($id)) {{ method_field('PUT') }} @endif
                <div class="layui-form-item">
                    <label class="layui-form-label">数据路径</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        <div class="xm-select-data-route"></div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">左表名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="left" required  lay-verify="required" autocomplete="off" placeholder="给起个好记的名字" class="layui-input" value="{{ $model->left ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">左表</label>
                        <div class="layui-input-block" style="width: 320px; margin-left: 96px;">
                            <div class="xm-select-left-table"></div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">左表模版</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        <textarea
                                name="left_tpl"
                                required
                                lay-verify="required"
                                autocomplete="off"
                                placeholder="用来显示的列，支持字段名+转换规则，
如：表列名有name、department，想要显示成「department」name前两个字符的拼接格式，
可以设置成 department::prepend:「|append:」+name::substr:0,2"
                                class="layui-textarea">{{ $model->left_tpl ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">关联关系</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        @include('core::admin.select', ['select_data' => light_dictionary('CORE_DICT_MODEL_RELATIONS'), 'select_name' => 'relationship', 'please_select' => '关联关系', 'select_required' => true, 'disabled' => true, 'model' => ['relationship' => $model->relationship ?? '']])
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">右表名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="right" required  lay-verify="required" autocomplete="off" placeholder="给起个好记的名字" class="layui-input" value="{{ $model->right ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">右表</label>
                        <div class="layui-input-block" style="width: 320px; margin-left: 96px;">
                            <div class="xm-select-right-table"></div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">右表模版</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        <textarea
                                name="right_tpl"
                                required
                                lay-verify="required"
                                autocomplete="off"
                                placeholder="用来显示的列，支持字段名+转换规则，
如：表列名有name、department，想要显示成「department」name前两个字符的拼接格式，
可以设置成 department::prepend:「|append:」+name::substr:0,2"
                                class="layui-textarea">{{ $model->right_tpl ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item display morphToMany morphTo morphOne morphMany">
                    <label class="layui-form-label">多态前缀</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        <input type="text" name="name" autocomplete="off" class="layui-input" value="{{ $model->name ?? config ('core.data_mapping.morphToMany.left')  }}">
                        <div class="layui-form-mid layui-color layui-word-aux">多态的列名前缀，例如 前缀_type, 前缀_id</div>
                    </div>
                </div>
                <div class="layui-form-item display morphToMany belongsToMany">
                    <label class="layui-form-label">表名</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        <input type="text" name="table" autocomplete="off" readonly="readonly" class="layui-input layui-bg-gray" value="{{ $model->table ?? config ('core.data_mapping.morphToMany.table', 'core_data_mappings')  }}">
                    </div>
                </div>
                <div class="layui-form-item display hasOneThrough hasManyThrough">
                    <label class="layui-form-label">中间模型</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        <input type="text" name="through" autocomplete="off" class="layui-input" value="{{ $model->through ?? ''  }}">
                        <div class="layui-form-mid layui-color layui-word-aux">模型的类路径 例如： App\Model\AdminUser</div>
                    </div>
                </div>
                <div class="layui-form-item display hasOneThrough hasManyThrough">
                    <label class="layui-form-label">First Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="first_key" autocomplete="off" class="layui-input" value="{{ $model->first_key ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display hasOneThrough hasManyThrough">
                    <label class="layui-form-label">Second Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="second_key" autocomplete="off" class="layui-input" value="{{ $model->second_key ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display hasOne hasMany belongsTo">
                    <label class="layui-form-label">Foreign Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="foreign_key" autocomplete="off" class="layui-input" value="{{ $model->foreign_key ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display belongsTo">
                    <label class="layui-form-label">Owner Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="owner_key" autocomplete="off" class="layui-input" value="{{ $model->owner_key ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display hasOneThrough hasOne hasManyThrough hasMany">
                    <label class="layui-form-label">Local Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="local_key" autocomplete="off" class="layui-input" value="{{ $model->local_key ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display hasOneThrough hasManyThrough">
                    <label class="layui-form-label">Second Local Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="second_local_key" autocomplete="off" class="layui-input" value="{{ $model->second_local_key ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display morphToMany belongsToMany">
                    <label class="layui-form-label">Foreign Pivot Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="foreign_pivot_key" autocomplete="off" class="layui-input" value="{{ $model->foreign_pivot_key ?? (config ('core.data_mapping.morphToMany.left') . '_id')  }}">
                    </div>
                </div>
                <div class="layui-form-item display morphToMany belongsToMany">
                    <label class="layui-form-label">Related Pivot Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="related_pivot_key" autocomplete="off" class="layui-input" value="{{ $model->related_pivot_key ?? (config ('core.data_mapping.morphToMany.right') . '_id')  }}">
                    </div>
                </div>
                <div class="layui-form-item display morphToMany belongsToMany">
                    <label class="layui-form-label">Parent Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="parent_key" placeholder="关联主表的主键名，如：id" autocomplete="off" class="layui-input" value="{{ $model->parent_key ?? ''  }}">
                    </div>
                </div>

                <div class="layui-form-item display morphToMany belongsToMany">
                    <label class="layui-form-label">Related Key</label>
                    <div class="layui-input-block">
                        <input type="text" name="related_key" autocomplete="off" placeholder="关联副表的主键名，如：id" class="layui-input" value="{{ $model->related_key ?? ''  }}">
                    </div>
                </div>

                <div class="layui-form-item display belongsToMany belongsTo">
                    <label class="layui-form-label">Relation</label>
                    <div class="layui-input-block">
                        <input type="text" name="relation" autocomplete="off" class="layui-input" value="{{ $model->relation ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        <textarea name="description" autocomplete="off" placeholder="这个数据映射是用来干啥的？什么都没写" class="layui-textarea">{{ $model->description ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">状态</label>
                    <div class="layui-input-block" style="margin-left: 96px;">
                        <input type="checkbox" name="status" lay-skin="switch" lay-text="启用|禁用" value="{{ Goodcatch\Modules\Core\Model\Admin\DataMap::STATUS_ENABLE }}" @if(isset($model) && $model->status == Goodcatch\Modules\Core\Model\Admin\DataMap::STATUS_ENABLE) checked @endif>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formDataMap" id="submitBtn">提交</button>
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
        form.on ('submit(formDataMap)', function (data){
            window.form_submit = $ ('#submitBtn');
            form_submit.prop ('disabled', true);
            data.field.status = data.field.status || {{Goodcatch\Modules\Core\Model\Admin\Datasource::STATUS_DISABLE}};
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

        function onSelectRelationship (data) {
            $ ('.layui-form-item.display').hide ().filter ('.' + data.value).show ();
        }

        onSelectRelationship ({'value':'{{isset ($model) ? $model->relationship : 'morphToMany'}}'});


        let has_data_route_xm = false;
        xmSelect.render ({
            el: '.layui-form .layui-input-block .xm-select-data-route',
            prop: {
                name: 'name',
                value: 'id',
            },
            radio: true,
            name: 'data_route_id',
            autoRow: true,
            clickClose: true,
            delay: 500,
            filterable: true,
            remoteSearch: true,
            remoteMethod: function (val, cb, show) {
                $.ajax({
                    url: '{{ route('admin::' . module_route_prefix ('.') . 'core.dataRoute.list') }}?limit=99999&keyword=' + val,
                    type: 'GET',
                    success: function (result) {

                        if (! has_data_route_xm)
                        {
                            has_data_route_xm = (result.data && result.data.length > 0);
                        }

                        if (has_data_route_xm)
                        {
                            @if(isset ($id) && isset ($model->data_route_id))
                            result.data.forEach ((d, k)=>{ if ((d.id + '') === '{{ $model->data_route_id }}') d.selected = true; });
                            @endif
                            cb (result.data);
                        } else {
                            $ ('.layui-input-block .xm-select-data-route')
                                .hide ()
                                .parent ()
                                .append ('<div class="layui-input layui-bg-gray" style="line-height: 35px;"><a class="layui-table-link" href="{{ route ('admin::' . module_route_prefix ('.') . 'core.dataRoute.create') }}">新增一个数据路径</a></div>')
                            ;
                        }
                    }
                });
            }
        });


        xmSelect.render ({
            el: '.layui-form .layui-input-block .xm-select-left-table',
            prop: {
                name: 'name',
                value: 'id',
            },
            @if(isset($id)) disabled: true, @endif
            radio: true,
            name: 'left_table',
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
                        @if(isset ($id) && isset ($model->left_table))
                        result.data.forEach ((d, k)=>{ if ((d.id + '') === '{{ $model->left_table }}') d.selected = true; });
                        @endif
                        cb (result.data);
                    }
                });
            }
        });

        xmSelect.render ({
            el: '.layui-form .layui-input-block .xm-select-right-table',
            prop: {
                name: 'name',
                value: 'id',
            },
            @if(isset($id)) disabled: true, @endif
            radio: true,
            name: 'right_table',
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
                        @if(isset ($id) && isset ($model->right_table))
                        result.data.forEach ((d, k)=>{ if ((d.id + '') === '{{ $model->right_table }}') d.selected = true; });
                        @endif
                        cb (result.data);
                    }
                });
            }
        });

    </script>
@endsection
