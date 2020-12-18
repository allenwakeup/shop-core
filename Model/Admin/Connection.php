<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

class Connection extends Model
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    const STRICT_TRUE = 1;
    const STRICT_FALSE = 0;

    const TYPE_SRC = 'SRC'; // 来源数据库
    const TYPE_DST = 'DST'; // 目标数据库

    const TYPE = [
        self::TYPE_SRC => '来源',
        self::TYPE_DST => '目标'
    ];

    protected $guarded = [];

    protected $casts = [
        'options' => 'array'
    ];

    /**
     * 搜索字段
     *
     * @var array
     */
    public static $searchField = [
        'database_id' => [
            'showType' => 'xm-select',
            'searchType' => '=',
            'title' => '数据源',
            'route' => 'admin::{{-$module_route_prefix-}}core.datasource.list',
            'params' => [
                'limit' => 99999
            ]
        ],
        'name' => '名称',
        'description' => '描述',
        'conn_type' => '连接类型',
        'tns' => 'TNS',
        'driver' => '驱动',
        'host' => '主机',
        'port' => '端口',
        'database' => '数据库',
        'username' => '用户名',
        'url' => 'URL',
        'service_name' => 'Service',
        'unix_socket' => 'Unix Socket',
        'charset' => '字符编码',
        'collation' => '字符集',
        'prefix' => '表前缀名',
        'prefix_schema' => 'Schema',
        'strict' => 'Strict',
        'engine' => 'Engine',
        'schema' => 'Schema',
        'edition' => '版本限制',
        'server_version' => '大版本号',
        'sslmode' => 'SSL Mode',
        'options' => '更多选项',
        'type' => [
            'showType' => 'select',
            'searchType' => '=',
            'title' => '分类',
            'enums' => self::TYPE,
        ],
        'group' => '分组',
        'order' => '排序',
        'status' => '状态'
    ];

    /**
     * 列表字段
     *
     * @var array
     */
    public static $listField = [
        'datasource' => [
            'title' => '数据源',
            'width' => 120,
            'sort' => true,
            'align' => 'center',
            'templet' => '#datasourceText'
        ],
        'name' => [
            'title' => '名称',
            'width' => 120,
            'sort' => true,
            'templet' => '#name'
        ],
        'typeText' => [
            'title' => '名称',

        ],
        'description' => [
            'title' => '描述',
            'width' => 150
        ],
        'driver' => [
            'title' => '驱动',
            'width' => 120
        ],
        'status' => [
            'title' => '状态',
            'width' => 120,
            'sort' => true,
            'align' => 'center',
            'templet' => '#statusSwitch',
            'event' => 'statusEvent'
        ],
        'conn_type' => [
            'title' => '连接类型',
            'width' => 110
        ],
        'tns' => [
            'title' => 'TNS',
            'width' => 120
        ],
        'host' => [
            'title' => '主机',
            'width' => 120
        ],
        'port' => [
            'title' => '端口',
            'width' => 90
        ],
        'database' => [
            'title' => '数据库',
            'width' => 120
        ],
        'username' => [
            'title' => '用户名',
            'width' => 120
        ],
        'url' => [
            'title' => 'URL',
            'width' => 120
        ],
        'service_name' => [
            'title' => 'Service',
            'width' => 110
        ],
        'unix_socket' => [
            'title' => 'Unix Socket',
            'width' => 120
        ],
        'charset' => [
            'title' => '字符编码',
            'width' => 120
        ],
        'collation' => [
            'title' => '字符集',
            'width' => 120
        ],
        'prefix' => [
            'title' => '表前缀名',
            'width' => 90
        ],
        'prefix_schema' => [
            'title' => 'Schema',
            'width' => 90
        ],
        'strict' => [
            'title' => 'Strict',
            'width' => 90
        ],
        'engine' => [
            'title' => 'Engine',
            'width' => 90
        ],
        'schema' => [
            'title' => 'Schema',
            'width' => 90
        ],
        'edition' => [
            'title' => '版本限制',
            'width' => 90
        ],
        'server_version' => [
            'title' => '大版本号',
            'width' => 90
        ],
        'sslmode' => [
            'title' => 'SSL Mode',
            'width' => 90
        ],
        'group' => [
            'title' => '分组',
            'width' => 120
        ],
        'order' => [
            'title' => '排序',
            'width' => 120
        ]
    ];

    public function datasource ()
    {
        return $this->belongsTo ('Goodcatch\Modules\Core\Model\Admin\Datasource');
    }

    public function data_routes ()
    {
        return $this->hasMany ('Goodcatch\Modules\Core\Model\Admin\DataRoute');
    }

    public function getConnectionNameAttribute ()
    {
        return $this->type . '_' . $this->name;
    }

    public function scopeOfEnabled ($query)
    {
        return $query->where ('status', self::STATUS_ENABLE);
    }

    public function getOptionsAttribute ($value)
    {
        $value = json_decode ($value, true);
        if (! isset ($value))
        {
            $value = [];
        }
        return $value;
    }

}
