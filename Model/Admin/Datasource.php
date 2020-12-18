<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

class Datasource extends Model
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;


    protected $guarded = [];

    /**
     * 搜索字段
     *
     * @var array
     */
    public static $searchField = [
        'code' => '代码',
        'name' => '名称',
        'description' => '描述',
        'requires' => '必填字段',
        'options' => '选填字段',
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
        'code' => [
            'title' => '代码',
            'width' => 120,
            'sort' => true
        ],
        'name' => [
            'title' => '名称',
            'width' => 120,
            'sort' => true
        ],
        'description' => [
            'title' => '描述',
            'width' => 150
        ],
        'requires' => [
            'title' => '必填项',
            'width' => 150
        ],
        'options' => [
            'title' => '可选项',
            'width' => 120
        ],
        'order' => [
            'title' => '排序',
            'width' => 80
        ],
        'status' => [
            'title' => '状态',
            'width' => 90,
            'sort' => true,
            'templet' => '#statusText'
        ],

    ];

}
