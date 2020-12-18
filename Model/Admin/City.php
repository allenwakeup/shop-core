<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Core\Model\Model;

class City extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'city';

    public function countys ()
    {
        return $this->hasMany ('Goodcatch\Modules\Core\Model\Admin\County', 'county_id', 'county_id');
    }

    public function province ()
    {
        return $this->belongsTo ('Goodcatch\Modules\Core\Model\Admin\Province', 'province_id', 'province_id');
    }
}
