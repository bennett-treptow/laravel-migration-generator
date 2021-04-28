# Version 3.1.6
### New Modifier
`useCurrentOnUpdate` has been implemented
### Bugfix
Issue #27 - `useCurrent` on `timestamps()` method fix

# Version 3.1.3
### [Timestamp:format] Removal
The [Timestamp:format] token for file names has been removed. Migration file names require that [Timestamp] be at the beginning in that specific format. Any other format would cause the migrations to not be loaded.


# Version 3.1.0
### Environment Variables
New environment variables:

| Key | Default Value | Allowed Values | Description |
| --- | ------------- | -------------- | ----------- |
| LMG_SKIP_VIEWS | false | boolean | When true, skip all views |
| LMG_SKIPPABLE_VIEWS | '' | comma delimited string | The views to be skipped |
| LMG_MYSQL_SKIPPABLE_VIEWS | null | comma delimited string | The views to be skipped when driver is `mysql` |
| LMG_SQLITE_SKIPPABLE_VIEWS | null | comma delimited string | The views to be skipped when driver is `sqlite` |
| LMG_PGSQL_SKIPPABLE_VIEWS | null | comma delimited string | The views to be skipped when driver is `pgsql` |
| LMG_SQLSRV_SKIPPABLE_VIEWS | null | comma delimited string | The views to be skipped when driver is `sqlsrv` |

# Version 3.0.0

### Run after migrations
When `LMG_RUN_AFTER_MIGRATIONS` is set to true, after running any of the `artisan migrate` commands, the `generate:migrations` command will be run using all the default options for the command. It will only run when the app environment is `local`.

### Environment Variables
New environment variables to replace config updates:

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
