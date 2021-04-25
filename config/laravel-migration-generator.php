<?php

return [
    'run_after_migrations' => env('LMG_RUN_AFTER_MIGRATIONS', false),
    'clear_output_path'    => env('LMG_CLEAR_OUTPUT_PATH', false),
    //default configs
    'table_naming_scheme' => env('LMG_TABLE_NAMING_SCHEME', '[Timestamp]_create_[TableName]_table.php'),
    'view_naming_scheme'  => env('LMG_VIEW_NAMING_SCHEME', '[Timestamp]_create_[ViewName]_view.php'),
    'path'                => env('LMG_OUTPUT_PATH', 'tests/database/migrations'),
    'skippable_tables'    => env('LMG_SKIPPABLE_TABLES', 'migrations'),
    'definitions'         => [
        'prefer_unsigned_prefix'              => env('LMG_PREFER_UNSIGNED_PREFIX', true),
        'use_defined_index_names'             => env('LMG_USE_DEFINED_INDEX_NAMES', true),
        'use_defined_foreign_key_index_names' => env('LMG_USE_DEFINED_FOREIGN_KEY_INDEX_NAMES', true),
        'use_defined_unique_key_index_names'  => env('LMG_USE_DEFINED_UNIQUE_KEY_INDEX_NAMES', true),
        'use_defined_primary_key_index_names' => env('LMG_USE_DEFINED_PRIMARY_KEY_INDEX_NAMES', true)
    ],

    //now driver specific configs
    //null = use default
    'mysql' => [
        'table_naming_scheme' => env('LMG_MYSQL_TABLE_NAMING_SCHEME', null),
        'view_naming_scheme'  => env('LMG_MYSQL_VIEW_NAMING_SCHEME', null),
        'path'                => env('LMG_MYSQL_OUTPUT_PATH', null),
        'skippable_tables'    => env('LMG_MYSQL_SKIPPABLE_TABLES', null)
    ],
    'sqlite' => [
        'table_naming_scheme' => env('LMG_SQLITE_TABLE_NAMING_SCHEME', null),
        'view_naming_scheme'  => env('LMG_SQLITE_VIEW_NAMING_SCHEME', null),
        'path'                => env('LMG_SQLITE_OUTPUT_PATH', null),
        'skippable_tables'    => env('LMG_MYSQL_SKIPPABLE_TABLES', null)
    ],
    'pgsql' => [
        'table_naming_scheme' => env('LMG_PGSQL_TABLE_NAMING_SCHEME', null),
        'view_naming_scheme'  => env('LMG_PGSQL_VIEW_NAMING_SCHEME', null),
        'path'                => env('LMG_PGSQL_OUTPUT_PATH', null),
        'skippable_tables'    => env('LMG_PGSQL_SKIPPABLE_TABLES', null)
    ],
    'sqlsrv' => [
        'table_naming_scheme' => env('LMG_SQLSRV_TABLE_NAMING_SCHEME', null),
        'view_naming_scheme'  => env('LMG_SQLSRV_VIEW_NAMING_SCHEME', null),
        'path'                => env('LMG_SQLSRV_OUTPUT_PATH', null),
        'skippable_tables'    => env('LMG_SQLSRV_SKIPPABLE_TABLES', null)
    ],
];
