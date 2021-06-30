<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;


class ScheduleLog extends Model
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 0;


    protected $guarded = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'core_schedule_logs';


    public function scopeOfSuccessful ($query) {
        return $query->where ('status', self::STATUS_SUCCESS);
    }
    public function scopeOfFailure ($query) {
        return $query->where ('status', self::STATUS_FAILED);
    }

}
