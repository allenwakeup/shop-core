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
            'searchType' => '='
        ]
    ];

}
