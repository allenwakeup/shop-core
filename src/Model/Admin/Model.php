<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

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

}
