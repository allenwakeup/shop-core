<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Core\Model\Model;

class Province extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'province';

    public function cities ()
    {
        return $this->hasMany ('Goodcatch\Modules\Core\Model\Admin\City', 'province_id', 'province_id');
    }


}
