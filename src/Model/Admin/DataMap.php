<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

class DataMap extends Model
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    const RELATIONSHIPS = [
        'morphToMany' => '多对多（多态）',
        'morphTo' => '一对多（多态）',
        'morphOne' => '一对一 (多态)',
        'morphMany' => '一对多（多态）',
        'hasOneThrough' => '远程一对一',
        'hasOne' => '一对一',
        'hasManyThrough' => '远程一对多',
        'hasMany' => '一对多',
        'belongsToMany' => '多对多',
        'belongsTo' => '一对多 (反向)'
    ];

    protected $guarded = [];

    /**
     * 搜索字段
     *
     * @var array
     */
    public static $searchField = [
        'data_route_id' => [
            'showType' => 'xm-select',
            'searchType' => '=',
            'title' => '数据路径',
            'route' => 'admin::{{-$module_route_prefix-}}core.dataRoute.list',
            'params' => [
                'limit' => 99999
            ]
        ],
        'left' => '左表名称',
        'left_table' => [
            'showType' => 'xm-select',
            'searchType' => '=',
            'title' => '左表',
            'route' => 'admin::{{-$module_route_prefix-}}core.database.list',
            'params' => [
                'limit' => 99999
            ]
        ],
        'description' => '描述',
        'left_tpl' => '左表模板',
        'right' => '右表名称',
        'right_table' => [
            'showType' => 'xm-select',
            'searchType' => '=',
            'title' => '右表',
            'route' => 'admin::{{-$module_route_prefix-}}core.database.list',
            'params' => [
                'limit' => 99999
            ]
        ],
        'right_tpl' => '右表模板',
        'relationship' => [
            'showType' => 'light_dictionary',
            'searchType' => '=',
            'title' => '关联关系',
            'dictionary' => 'CORE_DICT_MODEL_RELATIONS'
        ],
        'table' => '中间表',
        'status' => [
            'showType' => 'select',
            'searchType' => '=',
            'title' => '状态',
            'enums' => [
                self::STATUS_DISABLE => '禁用',
                self::STATUS_ENABLE => '启用',
            ],
        ],
    ];

    /**
     * 列表字段
     *
     * @var array
     */
    public static $listField = [
        'left' => [
            'title' => '左表名称',
            'width' => 150,
            'sort' => true
        ],
        'left_table' => [
            'title' => '左表',
            'width' => 180,
            'sort' => true
        ],
        'description' => [
            'title' => '描述',
            'width' => 220
        ],
        'status' => [
            'title' => '状态',
            'width' => 100,
            'sort' => true,
            'templet' => '#statusSwitch',
            'event' => 'statusEvent'
        ],
        'left_tpl' => [
            'title' => '左表模板',
            'width' => 120,
            'sort' => true
        ],
        'right' => [
            'title' => '右表名称',
            'width' => 150,
            'sort' => true
        ],
        'right_table' => [
            'title' => '右表',
            'width' => 180,
            'sort' => true
        ],
        'right_tpl' => [
            'title' => '右表模板',
            'width' => 120,
            'sort' => true
        ],
        'relationshipText' => [
            'title' => '关联关系',
            'width' => 150
        ],
        'table' => [
            'title' => '中间表',
            'width' => 120,
            'sort' => true
        ],
    ];

    public function scopeOfEnabled ($query)
    {
        return $query->where ('status', self::STATUS_ENABLE);
    }

    public function getTitleAttribute ()
    {
        return "为{$this->left}分配{$this->right}";
    }

    public function dataRoute ()
    {
        return $this->belongsTo('Goodcatch\Modules\Core\Model\Admin\DataRoute');
    }

}
