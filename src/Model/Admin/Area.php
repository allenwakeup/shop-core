<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;


class Area extends Model
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
        'code' => [
            'searchType' => '='
        ],
        'name' => '名称',
        'short' => '简称',
        'alias' => '别名',
        'description' => '描述'
    ];


    public function county ()
    {
        return $this->belongsTo('Goodcatch\Modules\Core\Model\Admin\County', 'code', 'county_id');
    }

}
