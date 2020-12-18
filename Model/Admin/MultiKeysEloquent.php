<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Core\Model\Concerns\HasCompositePrimaryKey;

/**
 * Job only, DO NOT use it
 *
 * for saving composite primary key model
 *
 * Class MultiKeysEloquent
 * @package App\Modules\Core\Model\Admin
 */
class MultiKeysEloquent extends Eloquent
{

    use HasCompositePrimaryKey;


}
