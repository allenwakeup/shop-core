<?php

return [
    'name' => 'Core',

    /*
    |--------------------------------------------------------------------------
    | 数据映射
    |--------------------------------------------------------------------------
    |
    |
    */
    'data_mapping' => [
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
    ],

    /*
    |--------------------------------------------------------------------------
    | Defined module services
    |--------------------------------------------------------------------------
    |
    | You can define module service for different provider in environment file.
    |
    */
    'modules' => [
        'service' => [
            'connection' => [
                'driver' => 'default',
                'providers' => [
                    'default' => 'lightcms'
                ],
                // override default
                'class' => 'Goodcatch\\Modules\\Core\\Lightcms\\Contracts\\Database\\ConnectionProvider'
            ],
        ],
    ]

];
