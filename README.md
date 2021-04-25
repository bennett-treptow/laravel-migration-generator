# Laravel Migration Generator
Generate migrations from existing database structures, an alternative to the schema dump provided by Laravel. A primary use case for this package would be a project that has many migrations that alter tables using `->change()` from doctrine/dbal that SQLite doesn't support and need a way to get table structures updated for SQLite to use in tests.
Another use case would be taking a project with a database and no migrations and turning that database into base migrations.

# Installation
```bash
composer require --dev bennett-treptow/laravel-migration-generator
```

```bash
php artisan vendor:publish --provider="LaravelMigrationGenerator\LaravelMigrationGeneratorProvider"
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

# Configuration

Each database driver can have separate configs, as specified in `config/laravel-migration-generator.php`.

Want to customize the migration stubs? Make sure you've published the vendor assets with the artisan command to publish vendor files above.

## Definition Output Preferences

| Key | Values | Description |
| --- | ------ | ----------- |
| prefer_unsigned_prefix | boolean | Set to true to use the `unsigned` prefix for the applicable fields (integer, smallInteger, etc). Set to false to use the `unsigned()` modifier. |
| use_defined_index_names | boolean | Set to true to use the defined index names as second parameter for the `->index()` method. Set to false to default to Laravel's index naming scheme. |
| use_defined_foreign_key_index_names | boolean | Set to true to use the defined index names for foreign keys as second parameter for the `->foreign()` method. Set to false to default to Laravel's index naming scheme. |
| use_defined_unique_key_index_names | boolean | Set to true to use the defined index names for unique keys as second parameter for the `->unique()` method. Set to false to default to Laravel's index naming scheme. |
| use_defined_primary_key_index_names | boolean | Set to true to use the defined index names for primary keys as second parameter for the `->primary()` method. Set to false to default to Laravel's index naming scheme. |

## Stubs
There is a default stub for tables and views, found in `resources/stubs/vendor/laravel-migration-generator/`.
Each database driver can be assigned a specific migration stub by creating a new stub file in `resources/stubs/vendor/laravel-migration-generator/` with a `driver`-prefix, e.g. `mysql-table.stub` for a MySQL specific table stub.

## Stub Naming
Stubs can be named using the `(table|view)_naming_scheme` in the config. See below for available tokens that can be replaced.

### Table Stubs
Table stubs have the following tokens available for the naming scheme:

| Token | Example | Description |
| ----- |-------- | ----------- |
| `[TableName]` | users | Table's name, same as what is defined in the database |
| `[TableName:Studly]` | Users | Table's name with `Str::studly()` applied to it (useful for standardizing table names if they are inconsistent) |
| `[TableName:Lowercase]` | users | Table's name with `strtolower` applied to it (useful for standardizing table names if they are inconsistent) |
| `[Timestamp]` | 2021_04_25_110000 | The standard migration timestamp format, at the time of calling the command: `Y_m_d_His` |
| `[Timestamp:{format}]` | {Y_m} = 2021_04 |Specify a format for the timestamp, e.g. \[Timestamp:Y_m\] |

Table schema stubs have the following tokens available:

| Token | Description |
| ----- | ----------- |
| `[TableName]` | Table's name, same as what is defined in the database |
| `[TableName:Studly]` | Table's name with `Str::studly()` applied to it, for use with the class name |
| `[Schema]` | The table's generated schema |


### View Stubs
View stubs have the following tokens available for the naming scheme:

| Token | Example | Description |
| ----- |-------- | ----------- |
| `[ViewName]` | user_earnings | View's name, same as what is defined in the database |
| `[ViewName:Studly]` | UserEarnings | View's name with `Str::studly()` applied to it (useful for standardizing view names if they are inconsistent) |
| `[ViewName:Lowercase]` | user_earnings | View's name with `strtolower` applied to it (useful for standardizing view names if they are inconsistent) |
| `[Timestamp]` | 2021_04_25_110000 | The standard migration timestamp format, at the time of calling the command: `Y_m_d_His` |
| `[Timestamp:{format}]` | {Y_m} = 2021_04 |Specify a format for the timestamp, e.g. \[Timestamp:Y_m\] |

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
