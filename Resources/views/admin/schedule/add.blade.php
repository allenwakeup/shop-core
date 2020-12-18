@extends('admin.base')
@section('css')
    <style>
        .layui-inline.cron .layui-form-label{
            width: auto;
        }
        .layui-inline.cron .layui-input-block{
            margin-left: 64px;
            width: 100px;
        }

        .layui-inline.cron-cycle .layui-input-inline {
            width: 120px;
        }
        .layui-inline.cron.week .layui-input-inline {
            width: 120px;
        }
        textarea[name="payload"] {
            min-height: 300px;
        }

    </style>
@endsection
@section('content')
    <div class="layui-card">

        @include('admin.breadcrumb')

        <div class="layui-card-body">
            <form class="layui-form" lay-filter="form" action="@if (isset ($id)){{ route ('admin::' . module_route_prefix ('.') . 'core.schedule.update', ['id' => $id]) }}@else{{ route ('admin::' . module_route_prefix ('.') . 'core.schedule.save') }}@endif" method="post">
                @if(isset($id)) {{ method_field('PUT') }} @endif
                <div class="layui-form-item">
                    <div class="layui-inline" style="width: 400px;">
                        <label class="layui-form-label">名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" required lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->name ?? ''  }}">
                        </div>
                    </div>
                    <div class="layui-inline show-options-switch">
                        <label class="layui-form-label">更多选项</label>
                        <div class="layui-input-inline">
                            <input type="checkbox" lay-skin="switch" lay-filter="options" lay-text="打开|关闭">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <textarea name="description" autocomplete="off" class="layui-textarea">{{ $model->description ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">任务类别</label>
                    <div class="layui-input-block">
                        @include('admin.select', ['select_data' => \collect (Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE)->map(function ($key, $value) {return ['id' => $value, 'name' => $key];}), 'select_name' => 'schedule_type', 'please_select' => '分类', 'model' => ['schedule_type' => $model->schedule_type ?? '']])
                    </div>
                </div>
                <div class="layui-form-item input input{{\Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_COMMAND}}" @if (! isset ($model) || \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_COMMAND !== $model->schedule_type) style="display: none;" @endif>
                    <label class="layui-form-label">指令</label>
                    <div class="layui-input-block">
                        <input type="text" name="input{{\Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_COMMAND}}" placeholder="执行指令，如：php artisan config:cache" autocomplete="off" class="layui-input" value="{{ $model->input ?? ''  }}" @if (isset ($model) && \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_COMMAND === $model->schedule_type) lay-verify="required" required @endif >
                    </div>
                </div>
                <div class="layui-form-item input input{{\Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_EXEC}}" @if (! isset ($model) || \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_EXEC !== $model->schedule_type) style="display: none;" @endif>
                    <label class="layui-form-label">脚本</label>
                    <div class="layui-input-block">
                        <textarea name="input{{\Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_EXEC}}" autocomplete="off" placeholder="执行脚本，如：npm run production" class="layui-textarea" @if (isset ($model) && \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_EXEC === $model->schedule_type) lay-verify="required" required @endif>{{ $model->input ?? ''  }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item input input{{\Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB}}" @if (! isset ($model) || \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB !== $model->schedule_type) style="display: none;" @endif>
                    <label class="layui-form-label">任务模板</label>
                    <div class="layui-input-block">
                        @include('admin.select', ['select_data' => light_dictionary('CORE_DICT_SCHEDULE_JOBS'), 'select_name' => 'input' . Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB, 'please_select' => '任务模板', 'select_required' => true, 'model' => ['input' . Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB => $model->input ?? '']])
                    </div>
                </div>
                <div class="layui-form-item input input{{\Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB}}" @if (! isset ($model) || \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB !== $model->schedule_type) style="display: none;" @endif>
                    <label class="layui-form-label">Payload</label>
                    <div class="layui-input-block" style="height: 300px">
                        <textarea name="payload" autocomplete="off" class="layui-textarea" @if (isset ($model) && \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB === $model->schedule_type) lay-verify="required" required @endif>@if(isset($model)) @if(isset ($model->payload)) @json($model->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) @endif @endif</textarea>
                        <div class="layui-form-mid layui-word-aux">
                            <span class="layui-badge">注</span>
                            <a href="javascript:;" onclick="payloadHelper ('payload_helper1', '输入表、输出表、队列任务配置参考')" class="layui-table-link">格式参考</a>
                            <a href="javascript:;" onclick="payloadHelper ('payload_helper2', '数据校验发送结果至邮件的配置参考')" class="layui-table-link">邮件校验格式参考</a>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline cron-cycle">
                        <label class="layui-form-label">执行周期</label>
                        <div class="layui-input-inline">
                            <div class="xm-select-cron-cycle"></div>
                        </div>
                    </div>
                    <div class="layui-inline cron week" style="display: none;">
                        <div class="layui-input-inline">
                            <div class="xm-select-cron-week"></div>
                        </div>
                    </div>
                    <div class="layui-inline cron day" style="display: none;">
                        <label class="layui-form-label">天</label>
                        <div class="layui-input-block">
                            <input type="number" name="cron_day" autocomplete="off" class="layui-input" value="3">
                        </div>
                    </div>
                    <div class="layui-inline cron hour">
                        <label class="layui-form-label">小时</label>
                        <div class="layui-input-block">
                            <input type="number" name="cron_hour" autocomplete="off" class="layui-input" value="1">
                        </div>
                    </div>
                    <div class="layui-inline cron minute">
                        <label class="layui-form-label">分钟</label>
                        <div class="layui-input-block">
                            <input type="number" name="cron_minute" autocomplete="off" class="layui-input" value="30">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item display options">
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">当前任务计划设置：{{ $model->cron ?? ''  }}</div>
                    </div>
                </div>
                <div class="layui-form-item display options" style="display: none;">
                    <div class="layui-inline">
                        <label class="layui-form-label">任务重复</label>
                        <div class="layui-input-block">
                            <input type="checkbox" name="overlapping" lay-skin="switch" lay-text="是|否" value="{{ Goodcatch\Modules\Core\Model\Admin\Schedule::OVER_LAPPING_ENABLE }}" @if(isset($model) && $model->overlapping == Goodcatch\Modules\Core\Model\Admin\Schedule::OVER_LAPPING_ENABLE) checked @endif>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">集群并发</label>
                        <div class="layui-input-block">
                            <input type="checkbox" name="one_server" lay-skin="switch" lay-text="是|否" value="{{ Goodcatch\Modules\Core\Model\Admin\Schedule::ONE_SERVER_ENABLE }}" @if(isset($model) && $model->one_server == Goodcatch\Modules\Core\Model\Admin\Schedule::ONE_SERVER_ENABLE) checked @endif>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">后台运行</label>
                        <div class="layui-input-block">
                            <input type="checkbox" name="background" lay-skin="switch" lay-text="是|否" value="{{ Goodcatch\Modules\Core\Model\Admin\Schedule::BACKGROUND_ENABLE }}" @if(isset($model) && $model->background == Goodcatch\Modules\Core\Model\Admin\Schedule::BACKGROUND_ENABLE) checked @endif>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">维护模式</label>
                        <div class="layui-input-block">
                            <input type="checkbox" name="maintenance" lay-skin="switch" lay-text="是|否" value="{{ Goodcatch\Modules\Core\Model\Admin\Schedule::MAINTENANCE_ENABLE }}" @if(isset($model) && $model->maintenance == Goodcatch\Modules\Core\Model\Admin\Schedule::MAINTENANCE_ENABLE) checked @endif>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item display options" style="display: none;">
                    <label class="layui-form-label">PING</label>
                    <div class="layui-input-block">
                        <textarea name="ping_before" autocomplete="off" placeholder="系统可以访问到的任意网页地址" class="layui-textarea">{{ $model->ping_before ?? ''  }}</textarea>
                        <div class="layui-form-mid layui-word-aux"><span class="layui-badge">注：</span>任务执行前 ping 指定的 网页地址，若地址可访问，任务才会执行</div>
                    </div>
                </div>

                <div class="layui-form-item display options" style="display: none;">
                    <label class="layui-form-label">PING</label>
                    <div class="layui-input-block">
                        <textarea name="ping_success" autocomplete="off" placeholder="系统可以访问到的任意网页地址" class="layui-textarea">{{ $model->ping_before ?? ''  }}</textarea>
                        <div class="layui-form-mid layui-word-aux"><span class="layui-badge">注：</span>任务执行成功后 ping 指定的 网页地址</div>
                    </div>
                </div>

                <div class="layui-form-item display options" style="display: none;">
                    <label class="layui-form-label">PING</label>
                    <div class="layui-input-block">
                        <textarea name="ping_failure" autocomplete="off" placeholder="系统可以访问到的任意网页地址" class="layui-textarea">{{ $model->ping_before ?? ''  }}</textarea>
                        <div class="layui-form-mid layui-word-aux"><span class="layui-badge">注：</span>任务执行失败后 ping 指定的 网页地址</div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">单次任务</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="once" lay-skin="switch" lay-text="启用|禁用" value="{{ Goodcatch\Modules\Core\Model\Admin\Schedule::ONCE_ENABLE }}" @if(isset($model) && $model->once == Goodcatch\Modules\Core\Model\Admin\Schedule::ONCE_ENABLE) checked @endif>
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
                        <input type="number" name="order" required lay-verify="required" autocomplete="off" class="layui-input" value="{{ $model->order ?? '0'  }}">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-block">
                            <input type="checkbox" name="status" lay-skin="switch" lay-text="启用|禁用" value="{{ Goodcatch\Modules\Core\Model\Admin\Schedule::STATUS_ENABLE }}" @if(isset($model) && $model->status == Goodcatch\Modules\Core\Model\Admin\Schedule::STATUS_ENABLE) checked @endif>
                        </div>
                    </div>
                    <div class="layui-inline input input{{\Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB}}" @if (! isset ($model) || \Goodcatch\Modules\Core\Model\Admin\Schedule::TYPE_JOB !== $model->schedule_type) style="display: none;" @endif>
                        <label class="layui-form-label">立即启动</label>
                        <div class="layui-input-block">
                            <input type="checkbox" name="start" lay-skin="switch" lay-text="启动|" value="start">
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formSchedule" id="submitBtn">提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
<script type="text/html" id="payload_helper1">
    <pre>
{
    "job":{
        "config":{
            "deleteWhenMissingModels":false,
            "_deleteWhenMissingModels_tips":"「忽略丢失的 Models」当向任务注入一个 Eloquent 模型时，它会在被放入队列之前自动序列化，并在处理任务时恢复。但是，如果在任务等待 worker 处理时删除了模型，你的任务可能会失败，出现 ModelNotFoundException。为方便起见，你可以通过设置你的任务的 deleteWhenMissingModels 属性为 true 来自动删除缺少模型的作业",
            "queue":"default",
            "_queue_tips":"指定队列名称，默认 default，队列名称必须在命令中指定，例如 php artisan queue:work --queue=default",
            "connection":"redis",
            "_connection_tips":"指定队列连接名称，默认 redis",
            "timeout":120,
            "_timeout_tips":"「超时」注意：timeout 功能针对 PHP 7.1 + 和 pcntl PHP 扩展进行了优化。同样，任务可以运行的最大秒数可以使用 Artisan 命令行上的 --timeout 开关来指定: php artisan queue:work --timeout=30 但是，你也可以定义允许任务在任务类本身上运行的最大秒数。如果在任务上指定了超时，它将优先于在命令行上指定的任何超时。",
            "tries":3,
            "_tries_tips":"「指定任务最大尝试次数 / 超时值」最大尝试次数:指定任务可尝试的最大次数的其中一个方法是，通过 Artisan 命令行上的 --tries 开关: php artisan queue:work --tries=3  但是，可以采用更细粒度的方法：定义任务类本身的最大尝试次数。如果在任务类上指定了最大尝试次数，它将优先于命令行上提供的值。",
            "run_if_previous_failure":"START_OF_TODAY",
            "_run_if_previous_failure_tips":"可选配置，仅当满足指定时间到当前时间内的相同任务计划最后一次执行失败的状态，才会执行数据交换。留空也表示不启用。"
        }
    },
    "source":{
        "name":"料品表",
        "_name_tips":"输入表的名称说明",
        "type":"模型名 支持 mongodb",
        "_type_tips":"可选，缺省值是 Eloquent 可选值有 Eloquent | MongoEloquent",
        "connection":"u9_db_middle",
        "_connection_tips":"定义在「数据连接」中的名称",
        "table":"items",
        "_table_tips":"输入表名称",
        "primary_key":"ID",
        "_primary_key_tips":"Eloquent 模型主键设置，不一定要与表的主键一致，可选择表中任意一列，缺省是 id",
        "key_type":"string",
        "_key_type_tips":"Eloquent 模型主键的数据类型，根据实际情况选择string，int等等，缺省是 int",
        "incrementing":false,
        "_incrementing_tips":"Eloquent 模型主键是否自增，根据实际情况设置true 或者 false，缺省是true",
        "period":{
            "created_at":{
                "unit":"HOUR",
                "start":"START_OF_TODAY",
                "end":"END_OF_TODAY"
            }
        },
        "_period_tips":"时间区间选项，仅当与时间区间设置有关的数据交换任务有效",
        "uses_timestamps":false,
        "conditions":{
            "select":[
                "ycode",
                "yname",
                "sum(ycode) as amount"
            ],
            "_select_tips":"select 可以是字符串、数组，当类型是字符串时与平时写的格式没有区别，例如 name as aname, concat(name,'_1') as bname",
            "group":[
                "ycode",
                "yname"
            ],
            "_group_tips":"group 可以是字符串、数组，当类型是字符串时与平时写的格式没有区别，例如 name, code",
            "where":[
                [
                    "title",
                    "<>",
                    ""
                ],
                [
                    "name",
                    "James, Bond"
                ],
                [
                    "created_at",
                    {
                        "method":"whereDate",
                        "value":"YESTERDAY",
                        "format":"Y-m-d H:i:s"
                    }
                ],
                [
                    "updated_at",
                    {
                        "method":"whereDate",
                        "value":"TODAY",
                        "format":"Y-m-d"
                    }
                ],
                [
                    "product_at",
                    {
                        "method":"whereColumn",
                        "value":"created_at"
                    }
                ]
            ],
            "_where_tips":"where 数组，每个数组的元素都是一维数组，大小2或者3。大小为3时：下标1为字段名，下标2为操作符，下标3为匹配值；大小为2时：操作符被设置成'='，下标1为字段名，下标2为匹配值；当大小为2，并且下标2类型是键值对时，进入高级模式：method缺省为where，其中可选的有orWhere、whereBetween、whereNotBetween、whereNotIn、whereNull、whereNotNull、whereDate、whereMonth、whereDay、whereYear、whereTime、whereColumn等等；value为匹配值，同时支持日期时间的简称，如NOW、TODAY、START_OF_TODAY、END_OF_TODAY、YESTERDAY、START_OF_YESTERDAY、END_OF_YESTERDAY、START_OF_WEEK、END_OF_WEEK、START_OF_MONTH、END_OF_MONTH、START_OF_YEAR、END_OF_YEAR；format为日期时间的格式化字符串，缺省为'Y-m-d H:i:s'",
            "whereNotNull":[
                [
                    "aname"
                ],
                [
                    "bname"
                ]
            ],
            "_whereNotNull_tips":"数组，每个元素为一维数组且只有一个元素；独立于where批量设置不为null的列，",
            "whereNull":[
                [
                    "aname"
                ],
                [
                    "bname"
                ]
            ],
            "_whereNull_tips":"数组，每个元素为一维数组且只有一个元素；独立于where批量设置为null的列，"
        },
        "fields_mapping":[
            [
                "code",
                "id",
                "trim|substr:0,2"
            ],
            [
                "ycode",
                "code"
            ],
            [
                "yname",
                "name"
            ]
        ],
        "unique":[
            [
                "ycode"
            ],
            [
                "department",
                "trim|substr:0,2"
            ],
            "md5"
        ]
    },
    "target":{
        "name":"增量同步产品到目标标准产品",
        "connection":"docker_mds_in",
        "table":"core_products",
        "primary_key":"id",
        "key_type":"string",
        "incrementing":false,
        "uses_timestamps":false,
        "unique":[
            [
                "unique:ycode-department",
                "id"
            ]
        ],
        "_clean_tips":"可选项，用于数据清洗，当数据同步完成，对当前数据列表进行清洗",
        "clean":{
            "field_name_1":{
                "where":4,
                "case":{
                    "when":[
                        {
                            "rules":{
                                "name":"starts_with:1,2,3,5,9"
                            },
                            "_rules_tips":"accepted 验证字段必须是 yes， on， 1，或 true。这在确认「服务条款」是否同意时相当有用。 active_url 根据 PHP 函数 dns_get_record ，验证字段必须具有有效的 A 或 AAAA 记录。 after:date 验证字段必须是给定日期之后的值。 after_or_equal:date 验证字段必须是在给定日期之后或与此日期相同的值。 alpha 验证字段必须完全由字母构成。 alpha_dash 验证字段可能包含字母、数字，以及破折号 (-) 和下划线 ( _ )。 alpha_num 验证字段必须是完全是字母、数字。 array 验证的字段必须是一个 PHP 数组。 before:date 正在验证的字段必须是给定日期之前的值。 before_or_equal:date 验证字段必须是在给定日期之前或与之相同的日期。这个日期值将会被传递给 PHP 的 strtotime 函数来计算。 between:min,max 验证字段的大小必须在给定的 min 和 max 之间。字符串、数字、数组和文件的计算方式都使用 size 方法。 boolean 验证的字段必须可以转换为 Boolean 类型。 可接受的输入为 true ， false ， 1 ， 0 ， “1” 和 “0” 。 date 验证的字段必须是有效的日期。 date_equals:date 验证字段必须等于给定日期。 date_format:format 验证字段必须匹配给定的日期格式。 different:field 验证的字段值必须与字段 field 的值不同。 digits:value 验证的字段必须为 numeric ，并且必须具有确切长度 value。 digits_between:min,max 验证字段的长度必须在给定的 min 和 max 之间。 可用的规则为:min_width, max_width, min_height, max_height, width, height, ratio。 ratio  约束应该表示为宽度除以高度。 这可以通过像 3/2 这样的语句或像 1.5 这样的 float 来指定： email 验证的字段必须符合 e-mail 地址格式。当前版本，此种验证规则由 egulias/email-validator 提供支持。默认使用 RFCValidation 验证样式，但你也可以使其他验证样式： ends_with:foo,bar,... 验证的字段必须以给定的值之一结尾。 filled 验证的字段在存在时不能为空。 gt:field 验证字段必须大于给定的 field。两个字段必须是相同的类型。字符串、数字、数组和文件都使用 size 进行相同的评估。 gte:field 验证字段必须大于或等于给定的 field。两个字段必须是相同的类型。字符串、数字、数组和文件都使用 size 进行相同的评估。 in:foo,bar,... 验证字段必须包含在给定的值列表中。由于此规则通常要求您 implode 数组，因此可以使用 Rule :: in 方法流畅地构造规则： integer 验证的字段必须是整数。 {注} 此种验证规则不是验证数据是 “integer” 类型，仅验证字符串或数值包含一个 integer. ip 验证的字段必须是 IP 地址。 ipv4 验证的字段必须是 IPv4 地址。 ipv6 验证的字段必须是 IPv6 地址。 json 验证的字段必须是有效的 JSON 字符串。 lt:field 验证的字段必须小于给定的 field.。这两个字段必须是相同的类型。字符串、数值、数组和文件大小的计算方式与 size 方法进行评估。 lte:field 验证中的字段必须小于或等于给定的 字段 。这两个字段必须是相同的类型。字符串、数值、数组和文件大小的计算方式与 size 方法进行评估。 max:value 验证中的字段必须小于或等于 value。字符串、数字、数组或是文件大小的计算方式都用 size 规则。 min:value 验证字段必须具有最小值。 字符串，数值，数组，文件大小的计算方式都与 size 规则一致. not_in:foo,bar,... 验证字段不能包含在给定的值的列表中。 使用 Rule::notIn 方法可以更流畅的构建这个规则： not_regex:pattern 验证字段必须与给定的正则表达式不匹配。例如：'not_regex:/^.+$/i'. nullable 验证字段可以为 null。这在验证基本数据类型时特别有用，例如可以包含空值的字符串和整数。 numeric 验证字段必须为数值。 present 验证字段必须存在于输入数据中，但可以为空。 regex:pattern 验证字段必须与给定的正则表达式匹配。例如：'not_regex:/^.+$/i' 。 required 验证的字段必须存在于输入数据中，而不是空。如果满足以下条件之一，则字段被视为「空」： 值为 null 。 值为空字符串。 required_if:anotherfield,value1,value2,value3... 如果其它字段 _anotherfield_ 为任一值 _value1_ 或 _value2_ 或 _value3_ 等（也可只有一个 _value1_） ，则此验证字段必须存在且不为空。 如果您需要构造更复杂的条件 required_if 规则， 您可以使用 Rule::requiredIf  required_unless:anotherfield,value,... 如果其它字段 _anotherfield_ 不等于任一值 _value_ ，则此验证字段必须存在且不为空。 required_with:foo,bar,... 在其他任一指定字段出现时，验证的字段才必须存在且不为空。 required_with_all:foo,bar,... 只有在其他指定字段全部出现时，验证的字段才必须存在且不为空。 required_without:foo,bar,... 在其他指定任一字段不出现时，验证的字段才必须存在且不为空。 required_without_all:foo,bar,... 只有在其他指定字段全部不出现时，验证的字段才必须存在且不为空。 same:field 验证字段必须与给定字段相匹配。 size:value 验证字段必须与给定值的大小一致。对于字符串，value 对应字符数。对于数字，value 对应给定的整数值。对于数组，size 对应数组的 count 值。对于文件，size 对应文件大小（单位 kb）。 starts_with:foo,bar,... 验证字段必须以给定值之一开头。 string 验证字段必须是一个字符串。如果允许这个字段为 null，需要给这个字段分配 nullable 规则。 timezone 验证字段必须为符合 PHP 函数 timezone_identifiers_list 所定义的有效时区标识。 url 验证的字段必须是有效的 URL。 uuid 验证字段必须是有效的 RFC 4122（版本 1,3,4 或 5）通用唯一标识符（UUID）。",
                            "then":1
                        }
                    ],
                    "else":2
                }
            },
            "_field_name_1_tips":"字段名，替换成目标表中的字段名",
            "field_name_2":{
                "where":4,
                "max":1,
                "_max_tips":"以此字段的最大值为基础，按设置的值累加"
            }
        }
    }
}
    </pre>

</script>
<script type="text/html" id="payload_helper2">
    <pre>
{
    "job":{
        "config":{
            "deleteWhenMissingModels":false,
            "_deleteWhenMissingModels_tips":"「忽略丢失的 Models」当向任务注入一个 Eloquent 模型时，它会在被放入队列之前自动序列化，并在处理任务时恢复。但是，如果在任务等待 worker 处理时删除了模型，你的任务可能会失败，出现 ModelNotFoundException。为方便起见，你可以通过设置你的任务的 deleteWhenMissingModels 属性为 true 来自动删除缺少模型的作业",
            "queue":"default",
            "_queue_tips":"指定队列名称，默认 default，队列名称必须在命令中指定，例如 php artisan queue:work --queue=default",
            "connection":"redis",
            "_connection_tips":"指定队列连接名称，默认 redis",
            "timeout":120,
            "_timeout_tips":"「超时」注意：timeout 功能针对 PHP 7.1 + 和 pcntl PHP 扩展进行了优化。同样，任务可以运行的最大秒数可以使用 Artisan 命令行上的 --timeout 开关来指定: php artisan queue:work --timeout=30 但是，你也可以定义允许任务在任务类本身上运行的最大秒数。如果在任务上指定了超时，它将优先于在命令行上指定的任何超时。",
            "tries":3,
            "_tries_tips":"「指定任务最大尝试次数 / 超时值」最大尝试次数:指定任务可尝试的最大次数的其中一个方法是，通过 Artisan 命令行上的 --tries 开关: php artisan queue:work --tries=3  但是，可以采用更细粒度的方法：定义任务类本身的最大尝试次数。如果在任务类上指定了最大尝试次数，它将优先于命令行上提供的值。",
            "run_if_previous_failure":"START_OF_TODAY",
            "_run_if_previous_failure_tips":"可选配置，仅当满足指定时间到当前时间内的相同任务计划最后一次执行失败的状态，才会执行数据交换。留空也表示不启用。"
        }
    },
    "double_check": {
        "send_msg": "WHEN_DIFF_VALUES",
        "_send_msg_tips": "可选参数，消息内容控制，可选值 WHEN_DIFF_ONLY WHEN_DIFF_VALUES，默认 WHEN_DIFF_ONLY",
        "send_msg_even_no_diff": false,
        "_send_msg_even_no_diff_tips": "可选参数，没有差异也发送消息，默认值 false",
        "msg_type": "EMAIL",
        "email_address": [
            "lw@huangyebl.com",
            "zq@huangyebl.com",
            "ldj@huangyebl.com"
        ],
        "sort": [
            "field_1",
            "field_2"
        ],
        "_sort_tips": "可选值，数组类型，使用系统排序，会增加时间消耗",
    },
    "source":{
        "name":"比对来源数据",
        "_name_tips":"输入表的名称说明",
        "connection":"u9_db_middle",
        "_connection_tips":"定义在「数据连接」中的名称",
        "sql": "select * from table_name where created_at >= :START_OF_TODAY and updated_at <= :END_OF_TODAY",
        "binding": [
            "START_OF_TODAY",
            {
                "name": "END_OF_TODAY",
                "format": "Y-m-d H:i:s"
            }
        ]
    },
    "target":{
        "name":"比对目标数据",
        "connection":"docker_mds_in",
        "sql": "select * from table_name"
    }
}
    </pre>

</script>
@section('js')
    <script>
        var form = layui.form;

        //监听提交
        form.on ('submit(formSchedule)', function (data){
            window.form_submit = $ ('#submitBtn');
            form_submit.prop ('disabled', true);

            data.field.input = data.field ['input' + data.field.schedule_type];

            let fillable_cron = getCronFields (data.field.cycle);

            data.field.cron = [
                fillable_cron.indexOf ('minute') > -1 ? ((data.field.cycle === 'minute-n' ? '*/' : '') + data.field.cron_minute) : '*',
                fillable_cron.indexOf ('hour') > -1 ? ((data.field.cycle === 'hour-n' ? '*/' : '') + data.field.cron_hour) : '*',
                fillable_cron.indexOf ('day') > -1 ? ((data.field.cycle === 'day-n' ? '*/' : '') + ((data.field.cron_day > 0 && data.field.cron_day <= 31) ? data.field.cron_day : '*')) : '*',
                '*',
                fillable_cron.indexOf ('week') > -1 ? data.field.week : '*'
            ].join (' ');

            data.field.overlapping = data.field.overlapping || {{Goodcatch\Modules\Core\Model\Admin\Schedule::OVER_LAPPING_DISABLE}};
            data.field.one_server = data.field.one_server || {{Goodcatch\Modules\Core\Model\Admin\Schedule::ONE_SERVER_DISABLE}};
            data.field.background = data.field.background || {{Goodcatch\Modules\Core\Model\Admin\Schedule::BACKGROUND_DISABLE}};
            data.field.maintenance = data.field.maintenance || {{Goodcatch\Modules\Core\Model\Admin\Schedule::MAINTENANCE_DISABLE}};
            data.field.once = data.field.once || {{Goodcatch\Modules\Core\Model\Admin\Schedule::ONCE_DISABLE}};
            data.field.status = data.field.status || {{Goodcatch\Modules\Core\Model\Admin\Schedule::STATUS_DISABLE}};

            delete data.field.cycle;
            getCronFields ().forEach (function (field) {
                delete data.field [field];
            });

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

        function onSelectScheduleType (data) {
            $ ('.layui-form-item.input, .layui-inline.input')
                .hide ()
                .find ('input,textarea')
                .removeAttr ('lay-verify')
                .removeAttr ('required')
            ;
            $ ('.layui-form-item.input.input' + data.value + ', .layui-inline.input.input' + data.value)
                .show ()
                .find ('input,textarea')
                .attr ({
                    'lay-verify': 'required',
                    'required': 'required'
                })
            ;
        }

        // 开关显示更多选项
        form.on ('switch(options)', function () {
            $ ('.layui-form .layui-form-item.display.options').css ('display', this.checked ? '' : 'none');
        });

        // 周期设置
        let cron_conf = {
            cycleArray: [['day', '每天'], ['day-n', 'N天'], ['hour', '每小时'], ['hour-n', 'N小时'], ['minute-n', 'N分钟'], ['week', '每星期'], ['month', '每月']],
            weekArray: [[1, '周一'], [2, '周二'], [3, '周三'], [4, '周四'], [5, '周五'], [6, '周六'], [7, '周日']]
        };

        function getCronFields (selected)
        {
            let fields = ['minute', 'hour'];
            switch (selected)
            {
                case 'day':
                    break;
                case 'day-n':
                    fields.push ('day');
                    break;
                case 'hour-n':
                    break;
                case 'hour':
                case 'minute-n':
                    fields = ['minute'];
                    break;
                case 'week':
                    fields.push (selected);
                    break;
                case 'month':
                    fields.push ('day');
                    break;
                default:
                    fields.push ('day');
                    fields.push ('week');
                    break;
            }
            return fields;
        }

        let xmSelectCycle = xmSelect.render({
            el: '.layui-input-inline .xm-select-cron-cycle',
            name: 'cycle',
            initValue: ['day'],
            style: {width: '120px'},
            theme: {
                color: window.GC_XM_SELECT_COLOR || '#1cbbb4',
            },
            template({item, sels, name, value}) {
                return item.name
                    + '<span style="position: absolute; right: 10px; color: #8799a3"></span>';
            },
            radio: true,
            autoRow: true,
            clickClose: true,
            data: cron_conf.cycleArray.reduce((arr, item) => {
                arr.push ({ "name": item [1], "value": item [0] });
                return arr;
            }, []),
            on: function (data) {
                if (data.arr.length > 0) {
                    $ ('.layui-inline.cron').hide ();
                    getCronFields (data.arr [0].value).forEach (function (field) {
                        if (field)
                        {
                            $ ('.layui-inline.cron.' + field).show ();
                        }
                    });
                }
            }
        });

        let xmSelectWeek = xmSelect.render({
            el: '.layui-input-inline .xm-select-cron-week',
            name: 'week',
            initValue: [1],
            theme: {
                color: window.GC_XM_SELECT_COLOR || '#1cbbb4',
            },
            template({item, sels, name, value}) {
                return item.name
                    + '<span style="position: absolute; right: 10px; color: #8799a3">'
                    + (item.value ? item.value : '') + '</span>';
            },
            style: { width: '120px' },
            radio: true,
            autoRow: true,
            clickClose: true,
            data: cron_conf.weekArray.reduce((arr, item) => {
                arr.push ({ "name": item [1], "value": item [0] });
                return arr;
            }, []),
            on: function (data) {
                if (data.arr.length > 0) {

                }
            }
        });

        function payloadHelper (selector_id, title)
        {
            layer.open({
                type: 1
                ,title: title
                ,skin: 'layui-layer-rim'
                ,area: ['80%', '80%']
                ,id: 'test'
                ,content: $ ('#' + selector_id).html ()
                ,btn: '关闭'
                ,btnAlign: 'c' //按钮居中
                ,shade: 0.2 //不显示遮罩
                ,moveType: 1
                ,yes: function (){
                    layer.closeAll ();
                }
            });
        }
    </script>
@endsection
