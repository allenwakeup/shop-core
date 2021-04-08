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
        'datasource_id.required' => 'Please select one :attribute',
        'conn_type.required' => 'Please select one :attribute',
        'requires.required' => 'Please list :attribute',
        'driver.required' => 'Please input :attribute',
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
        'charset' => 'Charset',
        'collation' => 'Collation',
        'conn_type' => 'Connection Type',
        'cron' => '定时器',
        // --- D ---
        'database' => 'Database Name',
        'datasource_id' => 'Data Source',
        'driver' => 'Database Driver',
        // --- E ---
        'engine' => 'Engine',

        // --- F ---

        // --- G ---

        // --- I ---

        // --- N ---

        // --- M ---

        // --- R ---
        'requires' => 'Required Fields',
        // --- O ---
        'options' => 'Optional Fields',

        // --- P ---
        'payload' => 'Payload',
        'port' => 'Port',
        'prefix' => 'Table Prefix',
        // --- S ---
        'schema' => 'Schema',
        'sslmode' => 'PGSql SSL Mode',
        'strict' => 'PGSql Strict',

        // --- T ---

        // --- U ---

        'unix_socket' => 'Socket File',
        'username' => 'User Name',
        // --- V ---

        // --- W ---



    ],

    'values' => [
        'schedule_type' => [
            3 => 'Job'
        ],
    ],

];
