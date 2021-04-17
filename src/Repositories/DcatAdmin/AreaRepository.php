<?php

namespace Goodcatch\Modules\Core\Repositories\DcatAdmin;

use Goodcatch\Modules\Core\Model\Admin\Area as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class AreaRepository extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
