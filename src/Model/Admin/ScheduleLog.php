<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;


use Carbon\Carbon;

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

    public function getCreatedAtHumanAttribute (){
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }
    public function getUpdatedAtHumanAttribute (){
        return Carbon::parse($this->attributes['updated_at'])->diffForHumans();
    }


}
