<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

class ClientGroup extends Model
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
        'name' => '名称'
    ];

    /**
     * 列表字段
     *
     * @var array
     */
    public static $listField = [
        'name' => [
            'title' => '名称',
            'sort' => true
        ]

    ];
}
