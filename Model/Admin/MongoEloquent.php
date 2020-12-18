<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Job only, DO NOT use it
 *
 * Class Eloquent
 * @package App\Modules\Core\Model\Admin
 */
class MongoEloquent extends Model
{

    /**
     * use timestamps
     *
     * @param $bl
     *
     * @return $this
     */
    public function setUsesTimestamps ($bl)
    {
        $this->timestamps = $bl;

        return $this;
    }

}
