<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Lightcms\Model\Admin\Model as BaseModel;

abstract class Model extends BaseModel
{

    /**
     * @var string module table with prefix
     */
    protected function getModuleTablePrefix () {
        return 'core_';
    }

}
