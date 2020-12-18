<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

use Goodcatch\Modules\Core\Model\Model;

class Attachment extends Model
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    protected $guarded = [];

    const ATTACHABLES_MAPPING = [
       // 'application' => Application::class,
        //'agreement' => Agreement::class,
    ];

    /**
     * Get all of the owning imageable models.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

}
