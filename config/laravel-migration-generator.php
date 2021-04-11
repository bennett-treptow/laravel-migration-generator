<?php

return [
    //default configs
    'table_naming_scheme' => '[Timestamp]_create_[TableName]_table.php',
    'view_naming_scheme'  => '[Timestamp]_create_[ViewName]_view.php',
    'path'                => base_path('tests/database/migrations'),
    'skippable_tables'    => [
        'migrations'
    ],

    //now driver specific configs
    //null = use default
    'mysql' => [
        'table_naming_scheme' => null,
        'view_naming_scheme'  => null,
        'path'                => null,
        'skippable_tables'    => null
    ],
    'sqlite' => [
        'table_naming_scheme' => null,
        'view_naming_scheme'  => null,
        'path'                => null,
        'skippable_tables'    => null
    ],
    'pgsql' => [
        'table_naming_scheme' => null,
        'view_naming_scheme'  => null,
        'path'                => null,
        'skippable_tables'    => null
    ],
    'sqlsrv' => [
        'table_naming_scheme' => null,
        'view_naming_scheme'  => null,
        'path'                => null,
        'skippable_tables'    => null
    ],
];
