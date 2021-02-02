<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Core\Model\Model;

class Attachment extends Model
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
        'name' => '名称',
        'size' => '大小',
        'ext_name' => '扩展名',
        'path' => '路径'
    ];

    /**
     * 列表字段
     *
     * @var array
     */
    public static $listField = [
        'name' => [
            'title' => '名称',
            'width' => 120,
            'sort' => true,
        ],
        'size' => [
            'title' => '大小',
            'width' => 80,
            'sort' => true,
        ],
        'ext_name' => [
            'title' => '扩展名',
            'width' => 100,
            'sort' => true,
        ],
        'path' => [
            'title' => '路径',
            'width' => 100,
            'sort' => true,
        ]
    ];

    /**
     * Get all of the owning imageable models.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

}
