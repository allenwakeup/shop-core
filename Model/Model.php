<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model;

use Goodcatch\Modules\Laravel\Model\Model as BaseModel;

abstract class Model extends BaseModel
{

    /**
     * @var string module table with prefix
     */
    protected $module_table_prefix = 'core_';


}
