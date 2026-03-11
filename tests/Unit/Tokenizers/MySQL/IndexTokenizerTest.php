<?php

namespace Tests\Tokenizers\MySQL;

use LaravelMigrationGenerator\Tokenizers\MySQL\IndexTokenizer;
use Tests\TestCase;

class IndexTokenizerTest extends TestCase
{
    // region Simple Index
    public function test_it_tokenizes_simple_index()
    {
        $indexTokenizer = IndexTokenizer::parse('KEY `password_resets_email_index` (`email`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('index', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());

        $this->assertEquals('$table->index([\'email\'], \'password_resets_email_index\')', $indexDefinition->render());
    }

    public function test_it_doesnt_use_index_name()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', false);
        $indexTokenizer = IndexTokenizer::parse('KEY `password_resets_email_index` (`email`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('index', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());

        $this->assertEquals('$table->index([\'email\'])', $indexDefinition->render());
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', true);
    }

    // endregion

    // region Primary Key
    public function test_it_tokenizes_simple_primary_key()
    {
        $indexTokenizer = IndexTokenizer::parse('PRIMARY KEY (`id`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('primary', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());

        $this->assertEquals('$table->primary([\'id\'])', $indexDefinition->render());
    }

    public function test_it_tokenizes_two_column_primary_key()
    {
        $indexTokenizer = IndexTokenizer::parse('PRIMARY KEY (`email`,`token`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('primary', $indexDefinition->getIndexType());
        $this->assertTrue($indexDefinition->isMultiColumnIndex());
        $this->assertCount(2, $indexDefinition->getIndexColumns());
        $this->assertEqualsCanonicalizing(['email', 'token'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->primary([\'email\', \'token\'])', $indexDefinition->render());
    }

    // endregion

    // region Unique Key
    public function test_it_tokenizes_simple_unique_key()
    {
        $indexTokenizer = IndexTokenizer::parse('UNIQUE KEY `users_email_unique` (`email`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('unique', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());

        $this->assertEquals('$table->unique([\'email\'], \'users_email_unique\')', $indexDefinition->render());
    }

    public function test_it_doesnt_use_unique_key_index_name()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_unique_key_index_names', false);
        $indexTokenizer = IndexTokenizer::parse('UNIQUE KEY `users_email_unique` (`email`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('unique', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());

        $this->assertEquals('$table->unique([\'email\'])', $indexDefinition->render());
        config()->set('laravel-migration-generator.definitions.use_defined_unique_key_index_names', true);
    }

    public function test_it_tokenizes_two_column_unique_key()
    {
        $indexTokenizer = IndexTokenizer::parse('UNIQUE KEY `users_email_location_id_unique` (`email`,`location_id`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('unique', $indexDefinition->getIndexType());
        $this->assertTrue($indexDefinition->isMultiColumnIndex());
        $this->assertCount(2, $indexDefinition->getIndexColumns());
        $this->assertEqualsCanonicalizing(['email', 'location_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->unique([\'email\', \'location_id\'], \'users_email_location_id_unique\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_two_column_unique_key_and_doesnt_use_index_name()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_unique_key_index_names', false);
        $indexTokenizer = IndexTokenizer::parse('UNIQUE KEY `users_email_location_id_unique` (`email`,`location_id`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('unique', $indexDefinition->getIndexType());
        $this->assertTrue($indexDefinition->isMultiColumnIndex());
        $this->assertCount(2, $indexDefinition->getIndexColumns());
        $this->assertEqualsCanonicalizing(['email', 'location_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->unique([\'email\', \'location_id\'])', $indexDefinition->render());
        config()->set('laravel-migration-generator.definitions.use_defined_unique_key_index_names', true);
    }

    // endregion

    // region Foreign Constraints
    public function test_it_tokenizes_foreign_key()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_bank_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertCount(1, $indexDefinition->getIndexColumns());
        $this->assertEquals('users', $indexDefinition->getForeignReferencedTable());
        $this->assertEquals(['id'], $indexDefinition->getForeignReferencedColumns());
        $this->assertEquals(['user_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->foreign(\'user_id\', \'fk_bank_accounts_user_id\')->references(\'id\')->on(\'users\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_foreign_key_doesnt_use_index_name()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_foreign_key_index_names', false);
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_bank_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertCount(1, $indexDefinition->getIndexColumns());
        $this->assertEquals('users', $indexDefinition->getForeignReferencedTable());
        $this->assertEquals(['id'], $indexDefinition->getForeignReferencedColumns());
        $this->assertEquals(['user_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->foreign(\'user_id\')->references(\'id\')->on(\'users\')', $indexDefinition->render());
        config()->set('laravel-migration-generator.definitions.use_defined_foreign_key_index_names', true);
    }

    public function test_it_tokenizes_foreign_key_with_update()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_bank_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertCount(1, $indexDefinition->getIndexColumns());
        $this->assertEquals('users', $indexDefinition->getForeignReferencedTable());
        $this->assertEquals(['id'], $indexDefinition->getForeignReferencedColumns());
        $this->assertEquals(['user_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->foreign(\'user_id\', \'fk_bank_accounts_user_id\')->references(\'id\')->on(\'users\')->onUpdate(\'cascade\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_foreign_key_with_delete()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_bank_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertCount(1, $indexDefinition->getIndexColumns());
        $this->assertEquals('users', $indexDefinition->getForeignReferencedTable());
        $this->assertEquals(['id'], $indexDefinition->getForeignReferencedColumns());
        $this->assertEquals(['user_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->foreign(\'user_id\', \'fk_bank_accounts_user_id\')->references(\'id\')->on(\'users\')->onDelete(\'cascade\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_foreign_key_with_update_and_delete()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_bank_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertCount(1, $indexDefinition->getIndexColumns());
        $this->assertEquals('users', $indexDefinition->getForeignReferencedTable());
        $this->assertEquals(['id'], $indexDefinition->getForeignReferencedColumns());
        $this->assertEquals(['user_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->foreign(\'user_id\', \'fk_bank_accounts_user_id\')->references(\'id\')->on(\'users\')->onUpdate(\'cascade\')->onDelete(\'cascade\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_foreign_key_with_multiple_columns()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `table2_ibfk_1` FOREIGN KEY (`table2-foreign1`, `table2-foreign2`) REFERENCES `table1` (`table1-field1`, `table1-field2`) ON DELETE CASCADE ON UPDATE CASCADE');
        $definition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $definition->getIndexType());
        $this->assertTrue($definition->isMultiColumnIndex());
        $this->assertCount(2, $definition->getIndexColumns());
        $this->assertEquals('table1', $definition->getForeignReferencedTable());
        $this->assertSame(['table1-field1', 'table1-field2'], $definition->getForeignReferencedColumns());
        $this->assertSame(['table2-foreign1', 'table2-foreign2'], $definition->getIndexColumns());
        $this->assertEquals('$table->foreign([\'table2-foreign1\', \'table2-foreign2\'], \'table2_ibfk_1\')->references([\'table1-field1\', \'table1-field2\'])->on(\'table1\')->onDelete(\'cascade\')->onUpdate(\'cascade\')', $definition->render());
    }

    public function test_it_tokenizes_foreign_key_with_update_restrict()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_bank_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertCount(1, $indexDefinition->getIndexColumns());
        $this->assertEquals('users', $indexDefinition->getForeignReferencedTable());
        $this->assertEquals(['id'], $indexDefinition->getForeignReferencedColumns());
        $this->assertEquals(['user_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->foreign(\'user_id\', \'fk_bank_accounts_user_id\')->references(\'id\')->on(\'users\')->onUpdate(\'restrict\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_foreign_key_with_update_set_null()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_bank_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE SET NULL');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertCount(1, $indexDefinition->getIndexColumns());
        $this->assertEquals('users', $indexDefinition->getForeignReferencedTable());
        $this->assertEquals(['id'], $indexDefinition->getForeignReferencedColumns());
        $this->assertEquals(['user_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->foreign(\'user_id\', \'fk_bank_accounts_user_id\')->references(\'id\')->on(\'users\')->onUpdate(\'set NULL\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_foreign_key_with_update_set_default()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_bank_accounts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE SET DEFAULT');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertCount(1, $indexDefinition->getIndexColumns());
        $this->assertEquals('users', $indexDefinition->getForeignReferencedTable());
        $this->assertEquals(['id'], $indexDefinition->getForeignReferencedColumns());
        $this->assertEquals(['user_id'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->foreign(\'user_id\', \'fk_bank_accounts_user_id\')->references(\'id\')->on(\'users\')->onUpdate(\'set DEFAULT\')', $indexDefinition->render());
    }

    // endregion

    // region Fulltext Index
    public function test_it_tokenizes_simple_fulltext_index()
    {
        $indexTokenizer = IndexTokenizer::parse('FULLTEXT KEY `posts_content_fulltext` (`content`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('fulltext', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertEquals(['content'], $indexDefinition->getIndexColumns());
        $this->assertEquals('posts_content_fulltext', $indexDefinition->getIndexName());

        $this->assertEquals('$table->fullText([\'content\'], \'posts_content_fulltext\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_fulltext_index_without_using_index_name()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', false);
        $indexTokenizer = IndexTokenizer::parse('FULLTEXT KEY `posts_content_fulltext` (`content`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('fulltext', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());

        $this->assertEquals('$table->fullText([\'content\'])', $indexDefinition->render());
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', true);
    }

    public function test_it_tokenizes_multi_column_fulltext_index()
    {
        $indexTokenizer = IndexTokenizer::parse('FULLTEXT KEY `posts_title_content_fulltext` (`title`,`content`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('fulltext', $indexDefinition->getIndexType());
        $this->assertTrue($indexDefinition->isMultiColumnIndex());
        $this->assertCount(2, $indexDefinition->getIndexColumns());
        $this->assertEqualsCanonicalizing(['title', 'content'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->fullText([\'title\', \'content\'], \'posts_title_content_fulltext\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_multi_column_fulltext_index_without_using_index_name()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', false);
        $indexTokenizer = IndexTokenizer::parse('FULLTEXT KEY `posts_title_content_fulltext` (`title`,`content`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('fulltext', $indexDefinition->getIndexType());
        $this->assertTrue($indexDefinition->isMultiColumnIndex());
        $this->assertCount(2, $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->fullText([\'title\', \'content\'])', $indexDefinition->render());
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', true);
    }

    // endregion

    // region Spatial Index
    public function test_it_tokenizes_simple_spatial_index()
    {
        $indexTokenizer = IndexTokenizer::parse('SPATIAL KEY `locations_coordinates_spatial` (`coordinates`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('spatial', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());
        $this->assertEquals(['coordinates'], $indexDefinition->getIndexColumns());
        $this->assertEquals('locations_coordinates_spatial', $indexDefinition->getIndexName());

        $this->assertEquals('$table->spatialIndex([\'coordinates\'], \'locations_coordinates_spatial\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_spatial_index_without_using_index_name()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', false);
        $indexTokenizer = IndexTokenizer::parse('SPATIAL KEY `locations_coordinates_spatial` (`coordinates`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('spatial', $indexDefinition->getIndexType());
        $this->assertFalse($indexDefinition->isMultiColumnIndex());

        $this->assertEquals('$table->spatialIndex([\'coordinates\'])', $indexDefinition->render());
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', true);
    }

    public function test_it_tokenizes_multi_column_spatial_index()
    {
        $indexTokenizer = IndexTokenizer::parse('SPATIAL KEY `locations_lat_lng_spatial` (`latitude`,`longitude`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('spatial', $indexDefinition->getIndexType());
        $this->assertTrue($indexDefinition->isMultiColumnIndex());
        $this->assertCount(2, $indexDefinition->getIndexColumns());
        $this->assertEqualsCanonicalizing(['latitude', 'longitude'], $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->spatialIndex([\'latitude\', \'longitude\'], \'locations_lat_lng_spatial\')', $indexDefinition->render());
    }

    public function test_it_tokenizes_multi_column_spatial_index_without_using_index_name()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', false);
        $indexTokenizer = IndexTokenizer::parse('SPATIAL KEY `locations_lat_lng_spatial` (`latitude`,`longitude`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('spatial', $indexDefinition->getIndexType());
        $this->assertTrue($indexDefinition->isMultiColumnIndex());
        $this->assertCount(2, $indexDefinition->getIndexColumns());

        $this->assertEquals('$table->spatialIndex([\'latitude\', \'longitude\'])', $indexDefinition->render());
        config()->set('laravel-migration-generator.definitions.use_defined_index_names', true);
    }

    // endregion

    // region CHECK Constraints
    public function test_it_ignores_check_constraints()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `example_table_chk_1` CHECK (json_valid(`data`))');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('check', $indexDefinition->getIndexType());
        $this->assertEquals('', $indexDefinition->render());
    }

    public function test_it_ignores_check_constraints_with_expression()
    {
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `orders_chk_1` CHECK ((`amount` > 0))');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('check', $indexDefinition->getIndexType());
        $this->assertEquals('', $indexDefinition->render());
    }

    // endregion

    // region Security - Index Name Escaping
    public function test_it_escapes_single_quotes_in_index_names()
    {
        $indexTokenizer = IndexTokenizer::parse('KEY `idx_test\'s_index` (`email`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('index', $indexDefinition->getIndexType());
        // The rendered output should escape the single quote to prevent PHP injection
        $this->assertEquals('$table->index([\'email\'], \'idx_test\\\'s_index\')', $indexDefinition->render());
    }

    public function test_it_escapes_single_quotes_in_fulltext_index_names()
    {
        $indexTokenizer = IndexTokenizer::parse('FULLTEXT KEY `idx_test\'s_fulltext` (`content`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('fulltext', $indexDefinition->getIndexType());
        // The rendered output should escape the single quote to prevent PHP injection
        $this->assertEquals('$table->fullText([\'content\'], \'idx_test\\\'s_fulltext\')', $indexDefinition->render());
    }

    public function test_it_escapes_single_quotes_in_spatial_index_names()
    {
        $indexTokenizer = IndexTokenizer::parse('SPATIAL KEY `idx_test\'s_spatial` (`coordinates`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('spatial', $indexDefinition->getIndexType());
        // The rendered output should escape the single quote to prevent PHP injection
        $this->assertEquals('$table->spatialIndex([\'coordinates\'], \'idx_test\\\'s_spatial\')', $indexDefinition->render());
    }

    public function test_it_escapes_single_quotes_in_column_names()
    {
        $indexTokenizer = IndexTokenizer::parse('KEY `test_index` (`col\'s_name`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('index', $indexDefinition->getIndexType());
        // The rendered output should escape the single quote in column name
        $this->assertEquals('$table->index([\'col\\\'s_name\'], \'test_index\')', $indexDefinition->render());
    }

    public function test_it_escapes_backslashes_in_index_names()
    {
        $indexTokenizer = IndexTokenizer::parse('KEY `idx\\test` (`email`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('index', $indexDefinition->getIndexType());
        // Backslashes should be escaped
        $this->assertEquals('$table->index([\'email\'], \'idx\\\\test\')', $indexDefinition->render());
    }

    public function test_it_escapes_backslashes_and_quotes_in_unique_index_names()
    {
        $indexTokenizer = IndexTokenizer::parse('UNIQUE KEY `idx\\\'test` (`email`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('unique', $indexDefinition->getIndexType());
        // Both backslashes and quotes should be escaped
        $this->assertEquals('$table->unique([\'email\'], \'idx\\\\\\\'test\')', $indexDefinition->render());
    }

    public function test_it_escapes_single_quotes_in_foreign_key_index_names()
    {
        config()->set('laravel-migration-generator.definitions.use_defined_foreign_key_index_names', true);
        $indexTokenizer = IndexTokenizer::parse('CONSTRAINT `fk_test\'s_key` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)');
        $indexDefinition = $indexTokenizer->definition();

        $this->assertEquals('foreign', $indexDefinition->getIndexType());
        // The rendered output should escape the single quote
        $this->assertEquals('$table->foreign(\'user_id\', \'fk_test\\\'s_key\')->references(\'id\')->on(\'users\')', $indexDefinition->render());
    }

    // endregion
}
