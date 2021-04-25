<?php

return [
    //default configs
    'table_naming_scheme' => '[Timestamp]_create_[TableName]_table.php',
    'view_naming_scheme'  => '[Timestamp]_create_[ViewName]_view.php',
    'path'                => base_path('tests/database/migrations'),
    'skippable_tables'    => [
        'migrations'
    ],
    'definitions' => [
        'prefer_unsigned_prefix'              => true,
        'use_defined_index_names'             => true,
        'use_defined_foreign_key_index_names' => true,
        'use_defined_unique_key_index_names'  => true,
        'use_defined_primary_key_index_names' => true
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
