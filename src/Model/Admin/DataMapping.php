<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Model\Admin;

class DataMapping extends ModelMapping
{
    const MORPH_LEFT = 'left';
    const MORPH_RIGHT = 'right';
    const TABLE = 'data_mappings';
    const MORPH_LEFT_ID = 'left_id';
    const MORPH_RIGHT_ID = 'right_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = self::TABLE;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'type_id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
