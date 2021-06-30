<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Illuminate\Support\Arr;

class Schedule extends Model
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    const ONCE_ENABLE = 1;
    const ONCE_DISABLE = 0;

    const TYPE_COMMAND = 1;
    const TYPE_EXEC = 2;
    const TYPE_JOB = 3;

    const TYPE = [
        self::TYPE_COMMAND => 'Command',
        self::TYPE_EXEC => 'Exec',
        self::TYPE_JOB => 'Job'
    ];

    const OVER_LAPPING_ENABLE = 1;
    const OVER_LAPPING_DISABLE = 0;

    const ONE_SERVER_ENABLE = 1;
    const ONE_SERVER_DISABLE = 0;

    const BACKGROUND_ENABLE = 1;
    const BACKGROUND_DISABLE = 0;

    const MAINTENANCE_ENABLE = 1;
    const MAINTENANCE_DISABLE = 0;

    protected $casts = [
        'payload' => 'array'
    ];

    protected $guarded = [];

    /**
     * 搜索字段
     *
     * @var array
     */
    public static $searchField = [
        'name' => '名称',
        'description' => '描述',
        'input' => '指令',
        'group' => '分组',
        'order' => '排序',
        'schedule_type' => [
            'showType' => 'light_dictionary',
            'searchType' => '=',
            'title' => '指令',
            'dictionary' => 'CORE_DICT_SCHEDULE_JOBS'
        ],
        'once' => [
            'showType' => 'select',
            'searchType' => '=',
            'title' => '单次任务',
            'enums' => [
                self::ONCE_DISABLE => '禁用',
                self::ONCE_ENABLE => '启用',
            ],
        ],
        'overlapping' => [
            'showType' => 'select',
            'searchType' => '=',
            'title' => '重复',
            'enums' => [
                self::OVER_LAPPING_DISABLE => '可重复',
                self::OVER_LAPPING_ENABLE => '不可重复',
            ],
        ],
        'one_server' => [
            'showType' => 'select',
            'searchType' => '=',
            'title' => '单服务器',
            'enums' => [
                self::ONE_SERVER_DISABLE => '多服务器执行',
                self::ONE_SERVER_ENABLE => '单服务器执行',
            ],
        ],
        'background' => [
            'showType' => 'select',
            'searchType' => '=',
            'title' => '后台执行',
            'enums' => [
                self::BACKGROUND_DISABLE => '前台执行',
                self::BACKGROUND_ENABLE => '后台执行',
            ],
        ],
        'maintenance' => [
            'showType' => 'select',
            'searchType' => '=',
            'title' => '维护模式',
            'enums' => [
                self::BACKGROUND_DISABLE => '不执行',
                self::BACKGROUND_ENABLE => '执行',
            ],
        ],
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
        'name' => [
            'title' => '名称',
            'width' => 300,
            'sort' => true,
            'templet' => '#name'
        ],
        'description' => [
            'title' => '描述',
            'width' => 480
        ],
        'status' => [
            'title' => '状态',
            'width' => 120,
            'sort' => true,
            'align' => 'center',
            'templet' => '#statusSwitch',
            'event' => 'statusEvent'
        ],
        'inputText' => [
            'title' => '指令',
            'width' => 120
        ],
        'cron' => [
            'title' => '执行周期',
            'width' => 120
        ],
        'schedule_type_text' => [
            'title' => '任务类型',
            'width' => 90
        ],
        'group' => [
            'title' => '分组',
            'width' => 120,
            'sort' => true
        ],
        'order' => [
            'title' => '排序',
            'width' => 80,
            'sort' => true
        ],
        'once' => [
            'title' => '单次任务',
            'width' => 100,
            'sort' => true,
            'align' => 'center',
            'templet' => '#onceText'
        ],
        'overlapping' => [
            'title' => '重复',
            'width' => 100,
            'sort' => true,
            'align' => 'center',
            'templet' => '#overlappingText'
        ],
        'one_server' => [
            'title' => '单服务器',
            'width' => 100,
            'sort' => true,
            'align' => 'center',
            'templet' => '#oneServerText'
        ],
        'background' => [
            'title' => '后台执行',
            'width' => 100,
            'sort' => true,
            'align' => 'center',
            'templet' => '#backgroundText'
        ],
        'maintenance' => [
            'title' => '维护模式',
            'width' => 100,
            'sort' => true,
            'align' => 'center',
            'templet' => '#maintenanceText'
        ],
    ];

    public function getPayloadAttribute ($value)
    {
        $value = json_decode ($value, true);
        if (is_null ($value) || is_string($value))
        {
            $value = [];
        }
        if (! Arr::has ($value, 'job.config'))
        {
            Arr::set ($value, 'job.config', [
                'deleteWhenMissingModels' => false,
                'queue' => 'default',
                'connection' => 'redis',
                'timeout' => 120,
                'tries' => 3,
                'retry_until' => 5 // retry job in seconds
            ]);
        }

        if (! Arr::has ($value, 'job.config.scheduleLogId') || strcmp(Arr::get ($this->attributes, 'id'), Arr::get ($value, 'job.config.scheduleLogId') !== 0))
        {
            Arr::set ($value, 'job.config.scheduleLogId', Arr::get ($this->attributes, 'id', 0));
        }
        if (! Arr::has ($value, 'job.config.scheduleLogName') || strcmp(Arr::get ($this->attributes, 'name'), Arr::get ($value, 'job.config.scheduleLogName') !== 0)) {
            Arr::set($value, 'job.config.scheduleLogName', Arr::get($this->attributes, 'name', ''));
        }
        return $value;
    }

    public function scopeOfEnabled ($query) {
        return $query->where ('status', self::STATUS_ENABLE);
    }
    public function scopeOfJob ($query) {
        return $query->where ('schedule_type', self::TYPE_JOB);
    }

    public function scopeOfOrdered ($query) {
        return $query->orderBy ('order', 'asc');
    }

    public function logs ()
    {
        return $this->hasMany ('Goodcatch\Modules\Core\Model\Admin\ScheduleLog', 'schedule_id', 'id');
    }
}
