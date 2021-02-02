<?php


namespace Goodcatch\Modules\Core\Model\Admin;


class Department extends Model
{
    protected $guarded = [];

    public static $searchField = [
        'name' => '名称',
    ];

    public static $listField = [
        'pid' => [
            'title' => '上级部门',
            'width' => 120,
            'sort' => true,
            'templet' => '#pidText'
        ],
        'name' => [
            'title' => '名称',
            'width' => 120,
            'sort' => true
        ],
        'order' => [
            'title' => '排序',
            'width' => 80
        ]
    ];

    public function parent()
    {
        return $this->belongsTo('Goodcatch\Modules\Core\Model\Admin\Department', 'pid');
    }

    public function children()
    {
        return $this->hasMany('Goodcatch\Modules\Core\Model\Admin\Department', 'pid');
    }

}