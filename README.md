# Laravel Migration Generator
![Latest Version on Packagist](https://img.shields.io/packagist/v/bennett-treptow/laravel-migration-generator.svg)

Generate migrations from existing database structures, an alternative to the schema dump provided by Laravel. A primary use case for this package would be a project that has many migrations that alter tables using `->change()` from doctrine/dbal that SQLite doesn't support and need a way to get table structures updated for SQLite to use in tests.
Another use case would be taking a project with a database and no migrations and turning that database into base migrations.

# Installation
```bash
composer require --dev bennett-treptow/laravel-migration-generator
```

```bash
php artisan vendor:publish --provider="LaravelMigrationGenerator\LaravelMigrationGeneratorProvider"
```
# Lumen Installation  
```bash  
composer require --dev bennett-treptow/laravel-migration-generator
```  
  
Copy config file from `vendor/bennett-treptow/laravel-migration-generator/config` to your Lumen config folder  
  
Register service provider in bootstrap/app.php  
```php  
$app->register(\LaravelMigrationGenerator\LaravelMigrationGeneratorProvider::class);  
```

# Usage

Whenever you have database changes or are ready to squash your database structure down to migrations, run:
```bash
php artisan generate:migrations
```

By default, the migrations will be created in `tests/database/migrations`. You can specify a different path with the `--path` option: 
```bash
php artisan generate:migrations --path=database/migrations
```

You can specify the connection to use as the database with the `--connection` option:
```bash
php artisan generate:migrations --connection=mysql2
```

You can also clear the directory with the `--empty-path` option:
```bash
php artisan generate:migrations --empty-path
```

This command can also be run by setting the `LMG_RUN_AFTER_MIGRATIONS` environment variable to `true` and running your migrations as normal. This will latch into the `MigrationsEnded` event and run this command using the default options specified via your environment variables. Note: it will only run when your app environment is set to `local`.

# Configuration

Want to customize the migration stubs? Make sure you've published the vendor assets with the artisan command to publish vendor files above.

## Environment Variables

| Key | Default Value | Allowed Values | Description |
| --- | ------------- | -------------- | ----------- |
| LMG_RUN_AFTER_MIGRATIONS | false | boolean | Whether or not the migration generator should run after migrations have completed. |
| LMG_CLEAR_OUTPUT_PATH | false | boolean | Whether or not to clear out the output path before creating new files. Same as specifying `--empty-path` on the command |
| LMG_TABLE_NAMING_SCHEME | `[Timestamp]_create_[TableName]_table.php` | string | The string to be used to name table migration files |
| LMG_VIEW_NAMING_SCHEME | `[Timestamp]_create_[ViewName]_view.php` | string | The string to be used to name view migration files |
| LMG_OUTPUT_PATH | tests/database/migrations | string | The path (relative to the root of your project) to where the files will be output to. Same as specifying `--path=` on the command |
| LMG_SKIPPABLE_TABLES | migrations | comma delimited string | The tables to be skipped |
| LMG_SKIP_VIEWS | false | boolean | When true, skip all views |
| LMG_SKIPPABLE_VIEWS | '' | comma delimited string | The views to be skipped |
| LMG_SORT_MODE | 'foreign_key' | string | The sorting mode to be used. Options: `foreign_key` |
| LMG_PREFER_UNSIGNED_PREFIX | true | boolean | When true, uses `unsigned` variant methods instead of the `->unsigned()` modifier. |
| LMG_USE_DEFINED_INDEX_NAMES | true | boolean | When true, uses index names defined by the database as the name parameter for index methods |
| LMG_USE_DEFINED_FOREIGN_KEY_INDEX_NAMES | true | boolean | When true, uses foreign key index names defined by the database as the name parameter for foreign key methods |
| LMG_USE_DEFINED_UNIQUE_KEY_INDEX_NAMES | true | boolean | When true, uses unique key index names defined by the database as the name parameter for the `unique` methods |
| LMG_USE_DEFINED_PRIMARY_KEY_INDEX_NAMES | true | boolean | When true, uses primary key index name defined by the database as the name parameter for the `primary` method |
| LMG_WITH_COMMENTS | true | boolean | When true, export comment using `->comment()` method. |
| LMG_USE_DEFINED_DATATYPE_ON_TIMESTAMP | false | boolean | When false, uses `->timestamps()` by mashing up `created_at` and `updated_at` regardless of  datatype defined by the database |
| LMG_MYSQL_TABLE_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_TABLE_NAMING_SCHEME when the database driver is `mysql`. |
| LMG_MYSQL_VIEW_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_VIEW_NAMING_SCHEME when the database driver is `mysql`. |
| LMG_MYSQL_OUTPUT_PATH | null | ?boolean | When not null, this setting will override LMG_OUTPUT_PATH when the database driver is `mysql`. |
| LMG_MYSQL_SKIPPABLE_TABLES | null | ?boolean | When not null, this setting will override LMG_SKIPPABLE_TABLES when the database driver is `mysql`. |
| LMG_MYSQL_SKIPPABLE_VIEWS | null | comma delimited string | The views to be skipped when driver is `mysql` |
| LMG_SQLITE_TABLE_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_TABLE_NAMING_SCHEME when the database driver is `sqlite`. |
| LMG_SQLITE_VIEW_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_VIEW_NAMING_SCHEME when the database driver is `sqlite`. |
| LMG_SQLITE_OUTPUT_PATH | null | ?boolean | When not null, this setting will override LMG_OUTPUT_PATH when the database driver is `sqlite`. |
| LMG_SQLITE_SKIPPABLE_TABLES | null | ?boolean | When not null, this setting will override LMG_SKIPPABLE_TABLES when the database driver is `sqlite`. |
| LMG_SQLITE_SKIPPABLE_VIEWS | null | comma delimited string | The views to be skipped when driver is `sqlite` |
| LMG_PGSQL_TABLE_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_TABLE_NAMING_SCHEME when the database driver is `pgsql`. |
| LMG_PGSQL_VIEW_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_VIEW_NAMING_SCHEME when the database driver is `pgsql`. |
| LMG_PGSQL_OUTPUT_PATH | null | ?boolean | When not null, this setting will override LMG_OUTPUT_PATH when the database driver is `pgsql`. |
| LMG_PGSQL_SKIPPABLE_TABLES | null | ?boolean | When not null, this setting will override LMG_SKIPPABLE_TABLES when the database driver is `pgsql`. |
| LMG_PGSQL_SKIPPABLE_VIEWS | null | comma delimited string | The views to be skipped when driver is `pgsql` |
| LMG_SQLSRV_TABLE_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_TABLE_NAMING_SCHEME when the database driver is `sqlsrc`. |
| LMG_SQLSRV_VIEW_NAMING_SCHEME | null | ?boolean | When not null, this setting will override LMG_VIEW_NAMING_SCHEME when the database driver is `sqlsrv`. |
| LMG_SQLSRV_OUTPUT_PATH | null | ?boolean | When not null, this setting will override LMG_OUTPUT_PATH when the database driver is `sqlsrv`. |
| LMG_SQLSRV_SKIPPABLE_TABLES | null | ?boolean | When not null, this setting will override LMG_SKIPPABLE_TABLES when the database driver is `sqlsrv`. |
| LMG_SQLSRV_SKIPPABLE_VIEWS | null | comma delimited string | The views to be skipped when driver is `sqlsrv` |

## Stubs
There is a default stub for tables and views, found in `resources/stubs/vendor/laravel-migration-generator/`.
Each database driver can be assigned a specific migration stub by creating a new stub file in `resources/stubs/vendor/laravel-migration-generator/` with a `driver`-prefix, e.g. `mysql-table.stub` for a MySQL specific table stub.

## Stub Naming
Table and view stubs can be named using the `LMG_(TABLE|VIEW)_NAMING_SCHEME` environment variables. Optionally, driver-specific naming schemes can be used as well by specifying `LMG_{driver}_TABLE_NAMING_SCHEME` environment vars using the same tokens. See below for available tokens that can be replaced.

### Table Name Stub Tokens
Table stubs have the following tokens available for the naming scheme:

| Token | Example | Description |
| ----- |-------- | ----------- |
| `[TableName]` | users | Table's name, same as what is defined in the database |
| `[TableName:Studly]` | Users | Table's name with `Str::studly()` applied to it (useful for standardizing table names if they are inconsistent) |
| `[TableName:Lowercase]` | users | Table's name with `strtolower` applied to it (useful for standardizing table names if they are inconsistent) |
| `[Timestamp]` | 2021_04_25_110000 | The standard migration timestamp format, at the time of calling the command: `Y_m_d_His` |
| `[Index]` | 0 | The key of the migration in the sorted order, for use with enforcing a sort order |
| `[IndexedEmptyTimestamp]` | 0000_00_00_000041 | The standard migration timestamp format, but filled with 0s and incremented by `[Index]` seconds |
| `[IndexedTimestamp]` | 2021_04_25_110003 | The standard migration timestamp format, at the time of calling the command: `Y_m_d_His` incremented by `[Index]` seconds |


### Table Schema Stub Tokens
Table schema stubs have the following tokens available:

| Token | Description |
| ----- | ----------- |
| `[TableName]` | Table's name, same as what is defined in the database |
| `[TableName:Studly]` | Table's name with `Str::studly()` applied to it, for use with the class name |
| `[TableUp]` | Table's `up()` function |
| `[TableDown]` | Table's `down()` function |
| `[Schema]` | The table's generated schema |


### View Name Stub Tokens
View stubs have the following tokens available for the naming scheme:

| Token | Example | Description |
| ----- |-------- | ----------- |
| `[ViewName]` | user_earnings | View's name, same as what is defined in the database |
| `[ViewName:Studly]` | UserEarnings | View's name with `Str::studly()` applied to it (useful for standardizing view names if they are inconsistent) |
| `[ViewName:Lowercase]` | user_earnings | View's name with `strtolower` applied to it (useful for standardizing view names if they are inconsistent) |
| `[Timestamp]` | 2021_04_25_110000 | The standard migration timestamp format, at the time of calling the command: `Y_m_d_His` |
| `[Index]` | 0 | The key of the migration in the sorted order, for use with enforcing a sort order |
| `[IndexedEmptyTimestamp]` | 0000_00_00_000041 | The standard migration timestamp format, but filled with 0s and incremented by `[Index]` seconds |
| `[IndexedTimestamp]` | 2021_04_25_110003 | The standard migration timestamp format, at the time of calling the command: `Y_m_d_His` incremented by `[Index]` seconds |

### View Schema Stub Tokens
View schema stubs have the following tokens available:

| Token | Description |
| ----- | ----------- |
| `[ViewName]` | View's name, same as what is defined in the database |
| `[ViewName:Studly]` | View's name with `Str::studly()` applied to it, for use with the class name |
| `[Schema]` | The view's schema |


# Example Usage

Given a database structure for a `users` table of:
```sql
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'America/New_York',
  `location_id` int(10) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_username_index` (`username`),
  KEY `users_first_name_index` (`first_name`),
  KEY `users_last_name_index` (`last_name`),
  KEY `users_email_index` (`email`),
  KEY `fk_users_location_id_index` (`location_id`)
  CONSTRAINT `users_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

A `tests/database/migrations/[TIMESTAMP]_create_users_table.php` with the following Blueprint would be created:
```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 128)->nullable()->index();
            $table->string('email', 255)->index();
            $table->string('password', 255);
            $table->string('first_name', 45)->nullable()->index();
            $table->string('last_name', 45)->index();
            $table->string('timezone', 45)->default('America/New_York');
            $table->unsignedInteger('location_id');
            $table->softDeletes();
            $table->string('remember_token', 255)->nullable();
            $table->timestamps();
            $table->foreign('location_id', 'users_location_id_foreign')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```


# Currently Supported DBMS's
These DBMS's are what are currently supported for creating migrations **from**. Migrations created will, as usual, follow what [database drivers Laravel migrations allow for](https://laravel.com/docs/8.x/database#introduction)

- [x] MySQL
- [ ] Postgres
- [ ] SQLite
- [ ] SQL Server
