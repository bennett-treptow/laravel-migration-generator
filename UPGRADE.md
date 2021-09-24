## Upgrade from 3.* to 4.0

### Foreign Key Sorting
New foreign key dependency sorting options, available as an env variable to potentially not sort by foreign key dependencies if not necessary.
Update your `config/laravel-migration-generator.php` to have a new `sort_mode` key:

```dotenv
'sort_mode' => env('LMG_SORT_MODE', 'foreign_key'),
```

### New Stubs
New stubs for a `create` and a `modify` version for tables.
If you want to change how a `Schema::create` or `Schema::table` is output as, create a new `table-create.stub` or `table-modify.stub` and their driver variants as well if desired.

### New Table and View Naming Tokens

Three new tokens were added for the table stubs: `[Index]`, `[IndexedEmptyTimestamp]`, and `[IndexedTimestamp]`.
For use with foreign key / sorting in general to enforce a final sorting.

`[Index]` is the numeric key (0,1,2,...) that the migration holds in the sort order.

`[IndexedEmptyTimestamp]` is the `[Index]` but prefixed with the necessary digits and underscores for the file to be recognized as a migration. `0000_00_00_000001_migration.php`

`[IndexedTimestamp]` is the current time incremented by `[Index]` seconds. So first migration would be the current time, second migration would be +1 second, third +2 seconds, etc.

### New Table Stub Tokens
Two new tokens were added for table stubs: `[TableUp]` and `[TableDown]`.
See latest `stubs/table.stub`. It is suggested to upgrade all of your stubs using the latest stubs available by `vendor:publish --force`

## Upgrade from 2.2.* to 3.0.0

`skippable_tables` is now a comma delimited string instead of an array so they are compatible with .env files.

All config options have been moved to equivalent .env variables. Please update `config/laravel-migration-generator.php` with a `vendor:publish --force`.
The new environment variables are below:

| Key | Default Value | Allowed Values | Description |
| --- | ------------- | -------------- | ----------- |
| LMG_RUN_AFTER_MIGRATIONS | false | boolean | Whether or not the migration generator should run after migrations have completed. |
| LMG_CLEAR_OUTPUT_PATH | false | boolean | Whether or not to clear out the output path before creating new files |
| LMG_TABLE_NAMING_SCHEME | [Timestamp]_create_[TableName]_table.php | string | The string to be used to name table migration files |
| LMG_VIEW_NAMING_SCHEME | [Timestamp]_create_[ViewName]_view.php | string | The string to be used to name view migration files |
| LMG_OUTPUT_PATH | tests/database/migrations | string | The path (relative to the root of your project) to where the files will be output to |
| LMG_SKIPPABLE_TABLES | migrations | comma delimited string | The tables to be skipped |
| LMG_PREFER_UNSIGNED_PREFIX | true | boolean | When true, uses `unsigned` variant methods instead of the `->unsigned()` modifier. |
| LMG_USE_DEFINED_INDEX_NAMES | true | boolean | When true, uses index names defined by the database as the name parameter for index methods |
| LMG_USE_DEFINED_FOREIGN_KEY_INDEX_NAMES | true | boolean | When true, uses foreign key index names defined by the database as the name parameter for foreign key methods |
| LMG_USE_DEFINED_UNIQUE_KEY_INDEX_NAMES | true | boolean | When true, uses unique key index names defined by the database as the name parameter for the `unique` methods |
| LMG_USE_DEFINED_PRIMARY_KEY_INDEX_NAMES | true | boolean | When true, uses primary key index name defined by the database as the name parameter for the `primary` method |
| LMG_MYSQL_TABLE_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_TABLE_NAMING_SCHEME when the database driver is `mysql`. |
| LMG_MYSQL_VIEW_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_VIEW_NAMING_SCHEME when the database driver is `mysql`. |
| LMG_MYSQL_OUTPUT_PATH | null | ?boolean | When not null, this setting will override LMG_OUTPUT_PATH when the database driver is `mysql`. |
| LMG_MYSQL_SKIPPABLE_TABLES | null | ?boolean | When not null, this setting will override LMG_SKIPPABLE_TABLES when the database driver is `mysql`. |
| LMG_SQLITE_TABLE_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_TABLE_NAMING_SCHEME when the database driver is `sqlite`. |
| LMG_SQLITE_VIEW_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_VIEW_NAMING_SCHEME when the database driver is `sqlite`. |
| LMG_SQLITE_OUTPUT_PATH | null | ?boolean | When not null, this setting will override LMG_OUTPUT_PATH when the database driver is `sqlite`. |
| LMG_SQLITE_SKIPPABLE_TABLES | null | ?boolean | When not null, this setting will override LMG_SKIPPABLE_TABLES when the database driver is `sqlite`. |
| LMG_PGSQL_TABLE_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_TABLE_NAMING_SCHEME when the database driver is `pgsql`. |
| LMG_PGSQL_VIEW_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_VIEW_NAMING_SCHEME when the database driver is `pgsql`. |
| LMG_PGSQL_OUTPUT_PATH | null | ?boolean | When not null, this setting will override LMG_OUTPUT_PATH when the database driver is `pgsql`. |
| LMG_PGSQL_SKIPPABLE_TABLES | null | ?boolean | When not null, this setting will override LMG_SKIPPABLE_TABLES when the database driver is `pgsql`. |
| LMG_SQLSRV_TABLE_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_TABLE_NAMING_SCHEME when the database driver is `sqlsrc`. |
| LMG_SQLSRV_VIEW_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_VIEW_NAMING_SCHEME when the database driver is `sqlsrv`. |
| LMG_SQLSRV_OUTPUT_PATH | null | ?boolean | When not null, this setting will override LMG_OUTPUT_PATH when the database driver is `sqlsrv`. |
| LMG_SQLSRV_SKIPPABLE_TABLES | null | ?boolean | When not null, this setting will override LMG_SKIPPABLE_TABLES when the database driver is `sqlsrv`. |
