# Laravel Migration Generator
Generate migrations from existing database structures, an alternative to the schema dump provided by Laravel. A primary use case for this package would be a project that has many migrations that alter tables using `->change()` from doctrine/dbal that SQLite doesn't support and need a way to get table structures updated for SQLite to use in tests.
Another use case would be taking a project with a database and no migrations and turning that database into base migrations.

# Installation

`composer require bennett-treptow/laravel-migration-generator`

# Usage

Whenever you have database changes or are ready to squash your database structure down to migrations, run:
`php artisan migrate:generate`

By default, the migrations will be created in `tests/database/migrations`. You can specify a different path with the `--path` option: `php artisan migrate:generate --path=database/migrations`

You can specify the connection to use as the database with the `--connection` option, `php artisan migrate:generate --connection=mysql2`


# Example

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

A `tests/database/migrations/0000_00_00_000000_create_test_users_table.php` with the following Blueprint would be created:
```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestActionRemindersTable extends Migration
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
