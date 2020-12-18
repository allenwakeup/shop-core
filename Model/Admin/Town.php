<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Core\Model\Model;

class Town extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'town';

    public function county ()
    {
        return $this->belongsTo ('Goodcatch\Modules\Core\Model\Admin\County', 'county_id', 'county_id');
    }
}
