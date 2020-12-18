<?php

return [
    'name' => 'Core',

    /*
    |--------------------------------------------------------------------------
    | 多对多 （多态）
    |--------------------------------------------------------------------------
    |
    |
    */
    'morphToMany' => [

        'model' => 'Goodcatch\Modules\Core\Model\Admin\Eloquent',

        'table' => 'core_data_mappings',

        'left' => 'left',

        'right' => 'right',

        'left_id' => 'left_id',

        'right_id' => 'right_id',

    ]
];
