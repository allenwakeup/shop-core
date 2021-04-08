<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'datasource_id.required' => '请选择一个 :attribute',
        'conn_type.required' => '请选择一个 :attribute',
        'requires.required' => '请罗列 :attribute',
        'driver.required' => '请选择 :attribute'
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        // --- A ---


        // --- C ---
        'charset' => '字符编码',
        'collation' => '字符集',
        'conn_type' => '连接类型',
        'cron' => '定时器',
        // --- D ---
        'database' => '数据库名',
        'datasource_id' => '数据源',
        'driver' => '数据库驱动',
        // --- E ---
        'engine' => 'Engine',

        // --- F ---

        // --- G ---

        // --- I ---

        // --- N ---

        // --- M ---

        // --- R ---
        'requires' => '必填字段',
        // --- O ---
        'options' => '选填字段',
        // --- P ---
        'payload' => 'Payload',
        'port' => '端口号',
        'prefix' => '表前缀名',
        // --- S ---
        'schema' => 'Schema',
        'sslmode' => 'PGSql SSL Mode',
        'strict' => 'PGSql Strict',

        // --- T ---

        // --- U ---

        'unix_socket' => 'Socket File',
        'username' => '用户名',
        // --- V ---

        // --- W ---
        


    ],

    'values' => [
        'schedule_type' => [
            3 => '队列任务'
        ],
    ],
];
