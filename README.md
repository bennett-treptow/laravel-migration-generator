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


# Currently Supported DBMS's
These DBMS's are what are currently supported for creating migrations **from**. Migrations created will, as usual, follow what [database drivers Laravel migrations allow for](https://laravel.com/docs/8.x/database#introduction)

- [x] MySQL
- [ ] Postgres
- [ ] SQLite
- [ ] SQL Server
