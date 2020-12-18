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
            'showType' => 'xm-select-region',
            'searchType' => '=',
            'title' => '地区'
        ],
        'name' => '名称',
        'short' => '简称',
        'alias' => '别名',
        'description' => '描述'
    ];

    /**
     * 列表字段
     *
     * @var array
     */
    public static $listField = [
        'region' => [
            'title' => '行政地区',
            'width' => 180,
            'sort' => true,
            'align' => 'center',
            'templet' => '#regionText'
        ],
        'name' => [
            'title' => '名称',
            'width' => 120,
            'sort' => true,
        ],
        'short' => [
            'title' => '简称',
            'width' => 80,
            'sort' => true,
        ],
        'alias' => [
            'title' => '别名',
            'width' => 100,
            'sort' => true,
        ],
        'display' => [
            'title' => '显示',
            'width' => 100,
            'sort' => true,
        ],
        'description' => [
            'title' => '描述',
            'width' => 150,
        ],
    ];

    public function region ()
    {
        return $this->hasOne ('Goodcatch\Modules\Core\Model\Admin\County', 'county_id', 'code');
    }

    public function factories ()
    {
        return $this->hasMany ('Goodcatch\Modules\Core\Model\Admin\Factory');
    }
}
