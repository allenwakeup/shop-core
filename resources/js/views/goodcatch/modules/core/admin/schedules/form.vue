<template>
    <div>
        <div class="admin_table_page_title">
            <a-button @click="$router.back()" class="float_right" icon="arrow-left">返回</a-button>
            计划与任务编辑
        </div>
        <div class="unline underm"></div>
        <div class="admin_form">
            <a-form-model ref="form" :model="form" :rules="rules" :label-col="{ span: 5 }" :wrapper-col="{ span: 12 }">
                <a-form-model-item label="名称" prop="name">
                    <a-row :gutter="8">
                        <a-col :span="12">
                            <a-input v-model="form.name" placeholder="请输入名称">
                                <a-tooltip slot="suffix" title="计划或任务名称，长度200个字符以内">
                                    <a-icon type="info-circle" />
                                </a-tooltip>
                            </a-input>
                        </a-col>
                        <a-col :span="12">
                            <a-switch checked-children="更多选项" un-checked-children="更少选项" @change="onChangeMoreOptions"/>
                        </a-col>
                    </a-row>
                </a-form-model-item>
                <a-form-model-item label="描述" prop="description">
                    <a-textarea v-model="form.description" placeholder="请输入计划或任务的描述信息，例如：用途，作用，周期说明等等。" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="执行" prop="schedule_type">
                    <a-input-group compact>
                        <a-select
                                placeholder="选择计划或任务的类型"
                                :value="form.schedule_type"
                                :model="form.schedule_type"
                                style="width: 100px"
                                @change="handleScheduleTypeChange">
                            <a-select-option :value="1">
                                执行指令
                            </a-select-option>
                            <a-select-option :value="2">
                                执行脚本
                            </a-select-option>
                            <a-select-option :value="3">
                                任务模板
                            </a-select-option>
                        </a-select>
                        <a-input v-model="form.input1" placeholder="执行指令，如 config:cache" v-show="showFormItem['input1']" style="width: 350px">
                            <a-tooltip slot="suffix" title="指令，当计划任务类型是命令时显示，如：php artisan config:cache 只需要输入 config:cache">
                                <a-icon type="info-circle" />
                            </a-tooltip>
                        </a-input>
                        <a-textarea v-model="form.input2" placeholder="请输入执行脚本，如：npm run production" :auto-size="{ minRows: 5, maxRows: 8 }" v-show="showFormItem['input2']" style="width: 400px"></a-textarea>
                        <a-select
                                placeholder="请选择任务模板"
                                :model="form.input3"
                                style="width: 180px"
                                :label-in-value="true"
                                @change="handleInput3Change" v-show="showFormItem['input3']">
                            <a-select-option value="App\\Modules\\Core\\Jobs\\ExchangeData">
                                数据交换
                            </a-select-option>
                            <a-select-option value="App\\Modules\\Core\\Jobs\\ExchangePeriodData">
                                时间段数据交换
                            </a-select-option>
                            <a-select-option value="App\\Modules\\Core\\Jobs\\DoubleCheckData">
                                数据校验
                            </a-select-option>
                        </a-select>
                    </a-input-group>
                </a-form-model-item>

                <a-form-model-item :label="form.input3Text" prop="payload" v-show="showFormItem['input3'] && form.input3">
                    <a-textarea v-model="form.payload" :auto-size="{ minRows: 8, maxRows: 12 }" />
                    <a-button type="link" @click="showPaylaodHelper1Modal" icon="info-circle">格式参考</a-button>
                    <a-button type="link" @click="showPaylaodHelper2Modal" icon="info-circle">邮件校验格式参考</a-button>
                    <a-modal
                            v-model="payloadHelper1"
                            title="输入表、输出表、队列任务配置参考"
                            :body-style="{ overflow: 'scroll', height: '500px' }"
                            :dialog-style="{ top: '20px' }"
                            width="80%"
                            height="600px">
                        <template slot="footer">
                            <a-button key="submit" type="primary" @click="handlePayloadHelperOk2">
                                知道了
                            </a-button>
                        </template>
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


                    </a-modal>
                    <a-modal
                            v-model="payloadHelper2"
                            title="数据校验发送结果至邮件的配置参考"
                            :body-style="{ overflow: 'scroll', height: '500px' }"
                            :dialog-style="{ top: '20px' }"
                            width="80%"
                            height="600px">
                        <template slot="footer">
                            <a-button key="submit" type="primary" @click="handlePayloadHelperOk2">
                                知道了
                            </a-button>
                        </template>
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

                    </a-modal>
                </a-form-model-item>
                <a-form-model-item label="执行周期">
                    <a-cron
                            ref="innerVueCron"
                            :value="form.cron"
                            @change="setCorn"></a-cron>
                </a-form-model-item>
                <a-form-model-item label="其他设置" v-show="showFormItem['overlapping']">
                    <a-input-group size="large">
                        <a-row :gutter="8">
                            <a-col :span="6">
                                <a-switch checked-children="重复任务/覆盖" un-checked-children="重复任务/等待" @change="onChangeOverlappingOptions" v-show="showFormItem['overlapping']"/>
                            </a-col>
                            <a-col :span="6">
                                <a-switch checked-children="单例模式" un-checked-children="集群模式" @change="onChangeOneServerOptions" v-show="showFormItem['one_server']"/>
                            </a-col>
                            <a-col :span="6">
                                <a-switch checked-children="后台模式" un-checked-children="前台模式" @change="onChangeBackgroundOptions" v-show="showFormItem['background']"/>
                            </a-col>
                            <a-col :span="6">
                                <a-switch checked-children="维护模式/运行" un-checked-children="维护模式/停止" @change="onChangeMaintenanceOptions" v-show="showFormItem['maintenance']"/>
                            </a-col>
                        </a-row>
                    </a-input-group>
                </a-form-model-item>
                <a-form-model-item label="PING" prop="ping_before" v-show="showFormItem['ping_before']">
                    <a-textarea v-model="form.ping_before" placeholder="任务执行前 ping一次系统可以访问到的任意网页地址，若地址可访问，任务才会执行。" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="PING" prop="ping_success" v-show="showFormItem['ping_success']">
                    <a-textarea v-model="form.ping_success" placeholder="任务执行成功后，ping一次系统可以访问到的任意网页地址。" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="PING" prop="ping_failure" v-show="showFormItem['ping_failure']">
                    <a-textarea v-model="form.ping_failure" placeholder="任务执行失败后 ping一次系统可以访问到的任意网页地址。" :auto-size="{ minRows: 3, maxRows: 5 }" />
                </a-form-model-item>
                <a-form-model-item label="分组" placeholder="对计划或任务进行分组设置，请输入分组名称" prop="group">
                    <a-input v-model="form.group"></a-input>
                </a-form-model-item>
                <a-form-model-item label="排序" prop="order">
                    <a-input-number v-model="form.order" :min="0" @change="onChangeOrder" />
                </a-form-model-item>
                <a-form-model-item label="状态">
                    <a-switch checked-children="启用" un-checked-children="禁用" :checked="form.status === 1" @change="onChangeStatus"/>
                </a-form-model-item>
                <a-form-model-item label="单次任务" v-show="form.status === 1">
                    <a-switch checked-children="是" un-checked-children="否" :checked="form.once === 1" @change="onChangeOnceOptions"/>
                </a-form-model-item>
                <a-form-model-item label="立即启动" v-show="id === 0 || form.schedule_type !== 3">
                    <a-switch checked-children="启动" un-checked-children="否" :checked="form.start === 1" @change="onChangeStartOptions"/>
                </a-form-model-item>
                <a-form-model-item :wrapper-col="{ span: 12, offset: 5 }">
                    <a-button type="primary" @click="handleSubmit">提交</a-button>
                </a-form-model-item>
            </a-form-model>

        </div>
    </div>
</template>

<script>
    import ACron from "@/components/goodcatch/modules/core/admin/acron";

    export default {
        components: { ACron },
        props: {},
        data() {
            return {
                formItemRequires: {
                    overlapping: false,
                    one_server: false,
                    background: false,
                    maintenance: false,
                    ping_before: false,
                    ping_success: false,
                    ping_failure: false
                },
                payloadHelper1: false,
                payloadHelper2: false,
                moreOption: false,
                form:{
                    name: '',
                    input1: '',
                    input2: '',
                    input3: undefined,
                    cron: '0 0 0 2 * ?',
                    ping_before: '',
                    ping_success: '',
                    ping_failure: '',
                    payload: '',
                    description: '',
                    schedule_type: 1,
                    overlapping: 0,
                    one_server: 0,
                    background: '',
                    maintenance: 0,
                    once: 1,
                    group: '',
                    order: 1,
                    status: 1,
                    start: 0
                },
                id:0,
                rules: {
                    group: [
                        {
                            required: true,
                            message: '请填写分组'
                        }
                    ]
                },
                test: {
                    loading: false
                }
            };
        },
        watch: {},
        computed: {

            showFormItem(){
                const items = {};
                for(const key in this.formItemRequires) {
                    if (this.formItemRequires.hasOwnProperty(key)) {
                        if (this.formItemRequires[key] === false && this.moreOption) {
                            items[key] = true;
                        }
                    }
                }
                [1, 2, 3].forEach(el => items ['input' + el] = (el === this.form.schedule_type));
                return items;
            }
        },
        methods: {
            handleSubmit(){

                this.$refs.form.validate(valid => {
                    if (valid) {
                        const params = Object.assign({}, this.form, {
                            logs: '',
                            payload: this.form.payload ? JSON.stringify(this.form.payload) : ''
                        });
                        let api = this.$apiHandle(this.$api.moduleCoreSchedules,this.id);
                        if(api.status){
                            this.$put(api.url, params).then(res=>{
                                if(res.code == 200){
                                    this.$message.success(res.msg)
                                    return this.$router.back();
                                }else{
                                    return this.$message.error(res.msg)
                                }
                            })
                        }else{
                            this.$post(api.url, params).then(res=>{
                                if(res.code == 200 || res.code == 201){
                                    this.$message.success(res.msg)
                                    return this.$router.back();
                                }else{
                                    return this.$message.error(res.msg);
                                }
                            })
                        }

                    } else {
                        this.$message.error('请正确填写表单！');
                        return false;
                    }
                });
            },

            onChangeMoreOptions(checked){
                this.moreOption = checked;
            },

            onChangeOverlappingOptions(checked){
                this.form.overlapping = checked ? 1 : 0;
            },

            onChangeOneServerOptions(checked){
                this.form.one_server = checked ? 1 : 0;
            },
            onChangeBackgroundOptions(checked){
                this.form.background = checked ? 1 : 0;
            },
            onChangeMaintenanceOptions(checked){
                this.form.maintenance = checked ? 1 : 0;
            },
            onChangeOnceOptions(checked){
                this.form.once = checked ? 1 : 0;
            },
            onChangeStartOptions(checked){
                this.form.start = checked ? 1 : 0;
            },

            onChangeOrder(value){
                this.form.order = value;
            },
            onChangePort(value){
                this.form.port = value;
            },
            onChangeStatus(checked){
                this.form.status = checked ? 1 : 0;
            },
            get_form(){
                this.$get(this.$api.moduleCoreSchedules+'/'+this.id).then(res=>{
                    res.data.payload = res.data.payload ? JSON.stringify(res.data.payload, null, 2) : '';
                    this.form = res.data;
                    if(res.data.schedule_type && res.data.input){
                        this.form['input' + res.data.schedule_type] = res.data.input;
                    }
                });
            },
            handleScheduleTypeChange(value){
                this.form.schedule_type = value;
            },
            handleInput3Change(kv) {
                this.form.input3 = kv.key;
                this.form.input3Text = kv.label;
            },
            showPaylaodHelper1Modal(){
                this.payloadHelper1 = true;
            },
            showPaylaodHelper2Modal(){
                this.payloadHelper2 = true;
            },
            handlePayloadHelperOk1(){
                this.payloadHelper1 = false;
            },
            handlePayloadHelperOk2(){
                this.payloadHelper2 = false;
            },
            setCorn(cron){
                this.form.cron = cron;
            },

            // 获取列表
            onload(){
                // 判断你是否是编辑
                if(!this.$isEmpty(this.$route.params.id)){
                    this.id = this.$route.params.id;
                    this.get_form();
                }
            },


        },
        created() {
            this.onload();
        },
        mounted() {}
    };
</script>
<style lang="scss" scoped>

    .payload-modal{
        width: 100%;
    }
</style>
