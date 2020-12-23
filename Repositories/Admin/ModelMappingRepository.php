<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Lightcms\Model\Admin\AdminUser;


class ModelMappingRepository extends BaseRepository
{

    public static function assignmentAdminUser ()
    {
        return AdminUser::query ()
            ->select (['id', 'name'])
            ->where (function ($query) {
                $query->where ('status', AdminUser::STATUS_ENABLE);
            })
            ->get ();
    }
}
