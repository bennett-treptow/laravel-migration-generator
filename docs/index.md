---
layout: default
title: Laravel Migration Generator
nav_order: 0
---
# Laravel Migration Generator

Generate migrations from existing database structures, an alternative to the schema dump provided by Laravel. This package will connect to your database and introspect the schema and generate migration files with columns and indices like they would be if they had originally come from a migration.

## Quick Start
```bash
composer require --dev bennett-treptow/laravel-migration-generator
php artisan vendor:publish --provider="LaravelMigrationGenerator\LaravelMigrationGeneratorProvider"
```

Learn more about [config options](config.md) and [stubs](stubs.md).