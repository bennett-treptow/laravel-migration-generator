# Laravel Migration Generator
Generate migrations from existing database structures, an alternative to the schema dump provided by Laravel.

# Installation

`composer require bennett-treptow/laravel-migration-generator`

# Usage

Whenever you have database changes or are ready to squash your database structure down to migrations, run:
`php artisan migrate:generate`

By default, the migrations will be created in `tests/database/migrations`. You can specify a different path with the `--path` option: `php artisan migrate:generate --path=database/migrations`

You can specify the connection to use as the database with the `--connection` option, `php artisan migrate:generate --connection=mysql2`


# Currently Supported DBMS's

[x] MySQL
[] Postgres
[] SQLite
[] SQL Server
