<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Core\Model\Model;

class County extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'county';

    public function towns ()
    {
        return $this->hasMany ('Goodcatch\Modules\Core\Model\Admin\Town', 'town_id', 'town_id');
    }

    public function city ()
    {
        return $this->belongsTo ('Goodcatch\Modules\Core\Model\Admin\City', 'city_id', 'city_id');
    }
}
