<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Illuminate\Support\Arr;

class DataRoute extends Model
{
    protected $guarded = [];

    /**
     * 搜索字段
     *
     * @var array
     */
    public static $searchField = [
        'name' => '路径名称',
        'alias' => '路径别名',
        'short' => '路径简称',
        'description' => '描述',
        'from' => '头表名称',
        'table_from' => [
            'showType' => 'xm-select',
            'searchType' => '=',
            'title' => '头表',
            'route' => 'admin::{{-$module_route_prefix-}}core.database.list',
            'params' => [
                'limit' => 99999
            ]
        ],
        'to' => '尾表名称',
        'table_to' => [
            'showType' => 'xm-select',
            'searchType' => '=',
            'title' => '尾表',
            'route' => 'admin::{{-$module_route_prefix-}}core.database.list',
            'params' => [
                'limit' => 99999
            ]
        ],
        'output' => [
            'showType' => 'xm-select',
            'searchType' => '=',
            'title' => '输出表',
            'route' => 'admin::{{-$module_route_prefix-}}core.database.list',
            'params' => [
                'limit' => 99999
            ]
        ],
        'connection_id' => [
            'showType' => 'xm-select',
            'searchType' => '=',
            'title' => '数据连接',
            'route' => 'admin::{{-$module_route_prefix-}}core.connection.list',
            'params' => [
                'limit' => 99999
            ]
        ]
    ];

    /**
     * 列表字段
     *
     * @var array
     */
    public static $listField = [
        'name' => [
            'title' => '路径名称',
            'width' => 150,
            'sort' => true
        ],
        'output' => [
            'title' => '输出表',
            'width' => 150,
            'sort' => true
        ],
        'table_from' => [
            'title' => '头表',
            'width' => 150,
            'sort' => true
        ],
        'table_to' => [
            'title' => '尾表',
            'width' => 150,
            'sort' => true
        ],
        'from' => '头表名称',
        'to' => '尾表名称',
        'alias' => '路径别名',
        'short' => '路径简称',
        'description' => '描述',
        'connection' => [
            'title' => '数据连接',
            'width' => 120,
            'sort' => true,
            'templet' => '#connectionText'
        ],
    ];

    public function getTitleAttribute ()
    {
        return "定义从{$this->from}到{$this->to}的数据路径";
    }

    public function getMenuAttribute ()
    {
        return "{$this->from}与{$this->to}";
    }

    public function getOutputAttribute ($value)
    {
        return 'sync_' . $value;
    }

    public function getOutputOriginAttribute ()
    {
        return Arr::get ($this->attributes, 'output');
    }

    public function dataMaps ()
    {
        return $this->hasMany('Goodcatch\Modules\Core\Model\Admin\DataMap');
    }

    public function connection ()
    {
        return $this->belongsTo ('Goodcatch\Modules\Core\Model\Admin\Connection');
    }
}
