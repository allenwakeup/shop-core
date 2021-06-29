<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use DateTimeInterface;
use Goodcatch\Modules\Laravel\Model\Model as BaseModel;

abstract class Model extends BaseModel
{

    /**
     * @return string module prefix of table name
     *
     */
    protected function getModuleTablePrefix () {
        return 'core_';
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
