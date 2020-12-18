<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Core\Model\Concerns\HasMappingAttributes;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * Job only, DO NOT use it
 *
 * Class Eloquent
 * @package App\Modules\Core\Model\Admin
 */
class Eloquent extends BaseModel
{

    use HasMappingAttributes, ForwardsCalls;

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
