@extends('admin.base')

@section('content')
    <div class="layui-card">

        @include('admin.breadcrumb')

        <div class="layui-card-body">
            <form class="layui-form" lay-filter="form" action="@if (isset ($id)){{ route ('admin::' . module_route_prefix ('.') . 'core.connection.update', ['id' => $id]) }}@else{{ route ('admin::' . module_route_prefix ('.') . 'core.connection.save') }}@endif" method="post">
                @if(isset($id)) {{ method_field('PUT') }} @endif
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">数据源</label>
                        <div class="layui-input-inline">
                            <div class="xm-select-datasource"></div>
                        </div>
                    </div>
                    <div class="layui-inline show-more-options-switch">
                        <label class="layui-form-label">更多选项</label>
                        <div class="layui-input-inline">
                            <input type="checkbox" lay-skin="switch" lay-filter="options" lay-text="打开|关闭">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" required lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->name ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display conn_type">
                    <label class="layui-form-label">连接类型</label>
                    <div class="layui-input-block">
                        <input type="text" name="conn_type" autocomplete="off" class="layui-input" value="{{ $model->conn_type ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display tns">
                    <label class="layui-form-label">TNS</label>
                    <div class="layui-input-block">
                        <textarea name="tns" autocomplete="off" class="layui-textarea">{{ $model->tns ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item display driver">
                    <label class="layui-form-label">驱动</label>
                    <div class="layui-input-block">
                        <input type="text" name="driver" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->driver ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline display host">
                        <label class="layui-form-label">主机</label>
                        <div class="layui-input-block">
                            <input type="text" name="host" autocomplete="off" class="layui-input" value="{{ $model->host ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline display port">
                        <label class="layui-form-label">端口号</label>
                        <div class="layui-input-block">
                            <input type="number" name="port" autocomplete="off" class="layui-input" value="{{ $model->port ?? ''  }}">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item display database">
                    <label class="layui-form-label">数据库名</label>
                    <div class="layui-input-block">
                        <input type="text" name="database" autocomplete="off" class="layui-input" value="{{ $model->database ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline display username">
                        <label class="layui-form-label">用户名</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" autocomplete="off" class="layui-input" value="{{ $model->username ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline display password">
                        <label class="layui-form-label">密码</label>
                        <div class="layui-input-block">
                            <input type="text" name="password" autocomplete="off" class="layui-input" value="{{ $model->password ?? ''  }}">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item display url">
                    <label class="layui-form-label">URL</label>
                    <div class="layui-input-block">
                        <input type="text" name="url" autocomplete="off" class="layui-input" value="{{ $model->url ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display service_name">
                    <label class="layui-form-label">Service</label>
                    <div class="layui-input-block">
                        <input type="text" name="service_name" autocomplete="off" class="layui-input" value="{{ $model->service_name ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display unix_socket">
                    <label class="layui-form-label">Socket文件</label>
                    <div class="layui-input-block">
                        <input type="text" name="unix_socket" autocomplete="off" class="layui-input" value="{{ $model->unix_socket ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display charset">
                    <label class="layui-form-label">字符编码</label>
                    <div class="layui-input-block">
                        <input type="text" name="charset" autocomplete="off" placeholder="utf8mb4" class="layui-input" value="{{ $model->charset ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display collation">
                    <label class="layui-form-label">字符集</label>
                    <div class="layui-input-block">
                        <input type="text" name="collation" autocomplete="off" placeholder="utf8mb4_unicode_ci" class="layui-input" value="{{ $model->collation ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display prefix">
                    <label class="layui-form-label">表前缀名</label>
                    <div class="layui-input-block">
                        <input type="text" name="prefix" autocomplete="off" class="layui-input" value="{{ $model->prefix ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display prefix_schema">
                    <label class="layui-form-label">Schema</label>
                    <div class="layui-input-block">
                        <input type="text" name="prefix_schema" autocomplete="off" class="layui-input" value="{{ $model->prefix_schema ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display strict">
                    <label class="layui-form-label">Strict</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="strict" lay-skin="switch" lay-text="是|否" value="1" @if(isset($model) && $model->strict == Goodcatch\Modules\Core\Model\Admin\Connection::STRICT_TRUE) checked @endif>
                    </div>
                </div>
                <div class="layui-form-item display engine">
                    <label class="layui-form-label">Engine</label>
                    <div class="layui-input-block">
                        <input type="text" name="engine" autocomplete="off" class="layui-input" value="{{ $model->engine ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display schema">
                    <label class="layui-form-label">Schema</label>
                    <div class="layui-input-block">
                        <input type="text" name="schema" autocomplete="off" placeholder="public" class="layui-input" value="{{ $model->schema ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display edition">
                    <label class="layui-form-label">版本限制</label>
                    <div class="layui-input-block">
                        <input type="text" name="edition" autocomplete="off" placeholder="ora$base" class="layui-input" value="{{ $model->edition ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display server_version">
                    <label class="layui-form-label">实例版本</label>
                    <div class="layui-input-block">
                        <input type="text" name="server_version" autocomplete="off" placeholder="11g" class="layui-input" value="{{ $model->server_version ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display sslmode">
                    <label class="layui-form-label">SSL Mode</label>
                    <div class="layui-input-block">
                        <input type="text" name="sslmode" autocomplete="off" placeholder="prefer" class="layui-input" value="{{ $model->sslmode ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item display options">
                    <label class="layui-form-label">其他选项</label>
                    <div class="layui-input-block">
                        <textarea name="options" autocomplete="off" placeholder="JSON格式" class="layui-textarea">@isset ($model) @json($model->options, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) @endisset</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">分类</label>
                    <div class="layui-input-block">
                        @include('core::admin.select', ['select_data' => \collect (Goodcatch\Modules\Core\Model\Admin\Connection::TYPE)->map(function ($key, $value) {return ['id' => $value, 'name' => $key];}), 'select_name' => 'type', 'please_select' => '分类', 'select_required' => true, 'model' => ['type' => $model->type ?? '']])
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">分组</label>
                    <div class="layui-input-block">
                        <input type="text" name="group" autocomplete="off" class="layui-input" value="{{ $model->group ?? ''  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">排序</label>
                    <div class="layui-input-block">
                        <input type="number" name="order" required  lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->order ?? '0'  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否启用</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="status" lay-skin="switch" lay-text="启用|禁用" value="1" @if(isset($model) && $model->status == Goodcatch\Modules\Core\Model\Admin\Connection::STATUS_ENABLE) checked @endif>
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
                        <button class="layui-btn" lay-submit lay-filter="formConnection" id="submitBtn">提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        <a class="layui-btn layui-btn-primary" onclick="test ('{{ route('admin::' . module_route_prefix ('.') . 'core.connection.test', ['action' => isset ($id) ? $id : 0]) }}', this)">测试</a>
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
        form.on ('submit(formConnection)', function (data){
            window.form_submit = $ ('#submitBtn');
            form_submit.prop ('disabled', true);
            if (! data.field.status)
            {
                data.field.status = {{Goodcatch\Modules\Core\Model\Admin\Connection::STATUS_DISABLE}};
            }
            if (! data.field.strict)
            {
                data.field.strict = {{Goodcatch\Modules\Core\Model\Admin\Connection::STRICT_FALSE}};
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

        form.on ('switch(options)', function () {
            $ ('.layui-form .layui-form-item.display.options').css ('display', this.checked ? '' : 'none');
        });

        $ ('.layui-inline.show-more-options-switch').hide ();

        let has_datasource_xm = false;
        xmSelect.render ({
            el: '.layui-form .layui-input-inline .xm-select-datasource',
            prop: {
                name: 'name',
                value: 'id',
            },
            radio: true,
            name: 'datasource_id',
            autoRow: true,
            clickClose: true,
            delay: 500,
            filterable: true,
            remoteSearch: true,
            on: function (data) {
                $ ('.layui-form .display')
                    .removeClass ('more-options')
                    .hide ()
                    .find ('input[required]')
                    .removeAttr ('required')
                    .removeAttr ('lay-verify');

                if (data.arr.length > 0) {
                    let data_selected = data.arr [0];
                    let initForm = {
                        "driver" : data_selected.code
                    };
                    if (data_selected.requires && data_selected.requires.length > 0) {
                        data_selected.requires.split(',').forEach (field => {
                            [key, value] = field.split (':');
                            $ ('.layui-form .display.' + key).show ();
                            initForm [key] = $ ('input[name=' + key + ']').attr ({
                                'lay-verify': 'required',
                                'required': 'required'
                            }).val () || value;
                        });
                    }
                    if (data_selected.options && data_selected.options.length > 0) {
                        data_selected.options.split(',').forEach (field => {
                            [key, value] = field.split (':');
                            $ ('.layui-form .display.' + key).addClass ('more-options').hide ();
                            initForm [key] = $ ('input[name=' + key + ']').val () || value;
                        });
                    }
                    form.val ('form', initForm);
                }
                $ ('.layui-inline.show-more-options-switch').css ('display', data.arr.length > 0 ? '' : 'none');
            },
            remoteMethod: function (val, cb, show) {
                $.ajax({
                    url: '{{ route('admin::' . module_route_prefix ('.') . 'core.datasource.list') }}?limit=99999&keyword=' + val,
                    type: 'GET',
                    success: function (result) {

                        if (! has_datasource_xm)
                        {
                            has_datasource_xm = (result.data && result.data.length > 0);
                        }

                        if (has_datasource_xm)
                        {
                            @if(isset ($id) && isset ($model->datasource_id))
                            result.data.forEach ((d, k)=>{ if ((d.id + '') === '{{ $model->datasource_id }}') d.selected = true; });
                            @endif
                            cb (result.data);
                        } else {
                            $ ('.layui-input-inline .xm-select-datasource')
                                .hide ()
                                .parent ()
                                .append ('<div class="layui-input layui-bg-gray" style="line-height: 35px;"><a class="layui-table-link" href="{{ route ('admin::' . module_route_prefix ('.') . 'core.datasource.create') }}">新增一个数据源</a></div>')
                            ;
                        }
                    }
                });
            }
        });


        function test (url, that) {

            let $this = $ (that);
            $this.find ('i').remove ();
            layer.load();

            let data = form.val ('form');
            delete data._method;
            data.status = data.status || {{Goodcatch\Modules\Core\Model\Admin\Connection::STATUS_DISABLE}};
            data.strict = data.strict || {{Goodcatch\Modules\Core\Model\Admin\Connection::STRICT_FALSE}};

            if (data.driver) {
                $.ajax ({
                    headers: {},
                    url: url,
                    type: 'get',
                    data: data,
                    success: function (result) {
                        layer.closeAll('loading');



                        if (result.code !== 0) {

                            layer.msg (result.msg, {shift: 6});
                            return false;
                        }
                        layer.msg (result.msg, {icon: 1});
                        $this.append ('<i class="layui-icon layui-icon-ok" style="margin-left: 5px;"></i>');

                    },
                    error: function (resp, stat, text) {
                        layer.closeAll('loading');
                        $this.append ('<i class="layui-icon layui-icon-help"></i>');
                        if (resp.status === 422) {
                            var parse = $.parseJSON(resp.responseText);
                            if (parse && parse.errors) {
                                var key = Object.keys(parse.errors)[0];
                                layer.msg(parse.errors[key][0], {shift: 6});
                            }
                            return false;
                        } else {
                            var parse = $.parseJSON(resp.responseText);
                            if (parse && parse.err) {
                                layer.alert(parse.msg);
                            }
                            return false;
                        }
                    },
                });
            }

        }
    </script>
@endsection
