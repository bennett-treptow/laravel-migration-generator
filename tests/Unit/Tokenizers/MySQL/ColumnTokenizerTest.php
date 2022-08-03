<?php

namespace Tests\Unit\Tokenizers\MySQL;

use Tests\TestCase;
use LaravelMigrationGenerator\Tokenizers\MySQL\ColumnTokenizer;

class ColumnTokenizerTest extends TestCase
{
    //region VARCHAR
    public function test_it_tokenizes_a_not_null_varchar_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('email', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertEquals('$table->string(\'email\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_varchar_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(200) COLLATE utf8mb4_unicode_ci');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertEquals(200, $columnDefinition->getMethodParameters()[0]);
        $this->assertNull($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertEquals('$table->string(\'favorite_color\', 200)', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT \'orange\'');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertEquals('orange', $columnDefinition->getDefaultValue());
        $this->assertEquals('$table->string(\'favorite_color\')->default(\'orange\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_null_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertEquals('$table->string(\'favorite_color\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_null_column_with_comment_with_setting()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'favorite color\'');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertEquals('favorite color', $columnDefinition->getComment());
        $this->assertEquals('$table->string(\'favorite_color\')->nullable()->comment("favorite color")', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_null_column_with_comment_without_setting()
    {
        config()->set('laravel-migration-generator.definitions.with_comments', false);
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'favorite color\'');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertEquals('favorite color', $columnDefinition->getComment());
        $this->assertEquals('$table->string(\'favorite_color\')->nullable()', $columnDefinition->render());

        config()->set('laravel-migration-generator.definitions.with_comments', true);
    }

    public function test_it_tokenizes_a_null_varchar_default_value_null_column_with_comment_apostrophe()
    {
        $columnTokenizer = ColumnTokenizer::parse("`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'favorite color is ''green''");
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertEquals('favorite color is \'green\'', $columnDefinition->getComment());
        $this->assertEquals('$table->string(\'favorite_color\')->nullable()->comment("favorite color is \'green\'")', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_null_column_with_comment_quotation()
    {
        $columnTokenizer = ColumnTokenizer::parse("`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'favorite color is \"green\"");
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertEquals('favorite color is "green"', $columnDefinition->getComment());
        $this->assertEquals('$table->string(\'favorite_color\')->nullable()->comment("favorite color is \"green\"")', $columnDefinition->render());
    }

    public function test_it_tokenizes_varchar_with_default_and_comment()
    {
        $columnTokenizer = ColumnTokenizer::parse("`testing` varchar(255) DEFAULT 'this is ''it''' COMMENT 'this is the \"comment\"'");
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('this is \'it\'', $columnDefinition->getDefaultValue());
        $this->assertEquals('this is the "comment"', $columnDefinition->getComment());
    }

    public function test_it_tokenizes_varchar_with_default_empty_string_and_comment()
    {
        $columnTokenizer = ColumnTokenizer::parse("`testing` varchar(255) DEFAULT '' COMMENT 'this is the \"comment\"'");
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('', $columnDefinition->getDefaultValue());
        $this->assertEquals('this is the "comment"', $columnDefinition->getComment());
    }

    public function test_it_tokenizes_varchar_with_boolean_literal_default()
    {
        $columnTokenizer = ColumnTokenizer::parse("`testing` bit(2) DEFAULT b'10'");
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('bit', $columnDefinition->getMethodName());
        $this->assertEquals('b\'10\'', $columnDefinition->getDefaultValue());
        $this->assertEquals("\$table->bit('testing', 2)->default(b'10')", $columnDefinition->render());
    }

    public function test_it_tokenizes_char_column_with_character_and_collation()
    {
        $columnTokenizer = ColumnTokenizer::parse('`country` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT \'US\'');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('country', $columnDefinition->getColumnName());
        $this->assertEquals('char', $columnTokenizer->getColumnDataType());
        $this->assertEquals('char', $columnDefinition->getMethodName());
        $this->assertCount(1, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertEquals('utf8mb4', $columnDefinition->getCharacterSet());
        $this->assertEquals('$table->char(\'country\', 2)->default(\'US\')', $columnDefinition->render());
    }

    //endregion

    //region TEXT & Variants
    public function test_it_tokenizes_a_not_null_tinytext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` tinytext NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('tinytext', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->string(\'notes\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_tinytext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` tinytext');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('tinytext', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->string(\'notes\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_text_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` text NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('text', $columnTokenizer->getColumnDataType());
        $this->assertEquals('text', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->text(\'notes\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_text_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` text');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('text', $columnTokenizer->getColumnDataType());
        $this->assertEquals('text', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->text(\'notes\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_mediumtext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` mediumtext NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('mediumtext', $columnTokenizer->getColumnDataType());
        $this->assertEquals('mediumText', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->mediumText(\'notes\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_mediumtext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` mediumtext');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('mediumtext', $columnTokenizer->getColumnDataType());
        $this->assertEquals('mediumText', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->mediumText(\'notes\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_longtext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` longtext NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('longtext', $columnTokenizer->getColumnDataType());
        $this->assertEquals('longText', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->longText(\'notes\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_longtext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` longtext');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('longtext', $columnTokenizer->getColumnDataType());
        $this->assertEquals('longText', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->longText(\'notes\')->nullable()', $columnDefinition->render());
    }

    //endregion

    //region INT & Variants
    public function test_it_tokenizes_a_not_null_smallint_without_param_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`cats` smallint NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('cats', $columnDefinition->getColumnName());
        $this->assertEquals('smallint', $columnTokenizer->getColumnDataType());
        $this->assertEquals('smallInteger', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->smallInteger(\'cats\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_smallint_with_param_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`cats` smallint(6) NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('cats', $columnDefinition->getColumnName());
        $this->assertEquals('smallint', $columnTokenizer->getColumnDataType());
        $this->assertEquals('smallInteger', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->smallInteger(\'cats\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_unsigned_smallint_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`cats` smallint(6) unsigned NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('cats', $columnDefinition->getColumnName());
        $this->assertEquals('smallint', $columnTokenizer->getColumnDataType());
        $this->assertEquals('smallInteger', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->unsignedSmallInteger(\'cats\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_nullable_big_int_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`template_id` bigint(20) unsigned DEFAULT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('template_id', $columnDefinition->getColumnName());
        $this->assertEquals('bigint', $columnTokenizer->getColumnDataType());
        $this->assertEquals('bigInteger', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertEquals('$table->unsignedBigInteger(\'template_id\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_primary_auto_inc_int_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`id` int(9) unsigned NOT NULL AUTO_INCREMENT');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('id', $columnDefinition->getColumnName());
        $this->assertEquals('int', $columnTokenizer->getColumnDataType());
        $this->assertEquals('integer', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isPrimary());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->unsignedInteger(\'id\')', $columnDefinition->render());
    }

    public function test_definition_config()
    {
        config()->set('laravel-migration-generator.definitions.prefer_unsigned_prefix', false);
        $columnTokenizer = ColumnTokenizer::parse('`column` int(9) unsigned NOT NULL');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('$table->integer(\'column\')->unsigned()', $columnDefinition->render());
        config()->set('laravel-migration-generator.definitions.prefer_unsigned_prefix', true);
    }

    //endregion

    //region FLOAT
    public function test_it_tokenizes_float_without_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float NOT NULL');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnDataType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_with_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2) NOT NULL');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnDataType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_null_without_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnDataType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_null_with_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2)');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnDataType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertNull($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_without_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float NOT NULL DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnDataType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals(1.0, $columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\')->default(1.00)', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_with_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2) NOT NULL DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnDataType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertEquals(1.00, $columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)->default(1.00)', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_null_without_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float DEFAULT 1.0');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnDataType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals(1.0, $columnDefinition->getDefaultValue());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\')->default(1.0)', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_null_with_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2) DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnDataType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertEquals(1.00, $columnDefinition->getDefaultValue());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)->default(1.00)', $columnDefinition->render());
    }

    //endregion

    //region DECIMAL
    public function test_it_tokenizes_a_not_null_decimal_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnDataType());
        $this->assertEquals('decimal', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->decimal(\'amount\', 9, 2)', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_unsigned_decimal_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) unsigned NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnDataType());
        $this->assertEquals('decimal', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->unsignedDecimal(\'amount\', 9, 2)', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_decimal_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) NOT NULL DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnDataType());
        $this->assertEquals('decimal', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals(1.0, $columnDefinition->getDefaultValue());
        $this->assertEquals('$table->decimal(\'amount\', 9, 2)->default(1.00)', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_unsigned_decimal_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) unsigned NOT NULL DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnDataType());
        $this->assertEquals('decimal', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertEquals(1.0, $columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->unsignedDecimal(\'amount\', 9, 2)->default(1.00)', $columnDefinition->render());
    }

    //endregion

    //region DOUBLE
    public function test_it_tokenizes_a_not_null_double_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnDataType());
        $this->assertEquals('double', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->double(\'amount\', 9, 2)', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_unsigned_double_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) unsigned NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnDataType());
        $this->assertEquals('double', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->double(\'amount\', 9, 2)->unsigned()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_double_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) NOT NULL DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnDataType());
        $this->assertEquals('double', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals(1.00, $columnDefinition->getDefaultValue());
        $this->assertEquals('$table->double(\'amount\', 9, 2)->default(1.00)', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_unsigned_double_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) unsigned NOT NULL DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnDataType());
        $this->assertEquals('double', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertEquals(1.00, $columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->double(\'amount\', 9, 2)->unsigned()->default(1.00)', $columnDefinition->render());
    }

    //endregion

    //region DATETIME
    public function test_it_tokenizes_a_not_null_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnDataType());
        $this->assertEquals('dateTime', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_datetime_default_now_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnDataType());
        $this->assertEquals('dateTime', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->useCurrent()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnDataType());
        $this->assertEquals('dateTime', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_default_value_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime DEFAULT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnDataType());
        $this->assertEquals('dateTime', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_default_value_now_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime DEFAULT CURRENT_TIMESTAMP');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnDataType());
        $this->assertEquals('dateTime', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->useCurrent()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_default_value_now_and_on_update_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnDataType());
        $this->assertEquals('dateTime', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->useCurrent()->useCurrentOnUpdate()', $columnDefinition->render());
    }

    //endregion

    //region TIMESTAMP
    public function test_it_tokenizes_a_not_null_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnDataType());
        $this->assertEquals('timestamp', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnDataType());
        $this->assertEquals('timestamp', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertNull($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_use_current_timestamp_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnDataType());
        $this->assertEquals('timestamp', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')->useCurrent()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_default_value_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NOT NULL DEFAULT \'2000-01-01 00:00:01\'');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnDataType());
        $this->assertEquals('timestamp', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals('2000-01-01 00:00:01', $columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')->default(\'2000-01-01 00:00:01\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_default_value_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NULL DEFAULT \'2000-01-01 00:00:01\'');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnDataType());
        $this->assertEquals('timestamp', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals('2000-01-01 00:00:01', $columnDefinition->getDefaultValue());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')->nullable()->default(\'2000-01-01 00:00:01\')', $columnDefinition->render());
    }

    //endregion

    //region ENUM
    public function test_it_tokenizes_enum_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\')');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('status_flag', $columnDefinition->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnDataType());
        $this->assertEquals('enum', $columnDefinition->getMethodName());
        $this->assertCount(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnDefinition->getMethodParameters()[0]);
        $this->assertNull($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])', $columnDefinition->render());
    }

    public function test_it_tokenizes_not_null_enum_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\') NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('status_flag', $columnDefinition->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnDataType());
        $this->assertEquals('enum', $columnDefinition->getMethodName());
        $this->assertCount(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnDefinition->getMethodParameters()[0]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])', $columnDefinition->render());
    }

    public function test_it_tokenizes_enum_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\') DEFAULT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('status_flag', $columnDefinition->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnDataType());
        $this->assertEquals('enum', $columnDefinition->getMethodName());
        $this->assertCount(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnDefinition->getMethodParameters()[0]);
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_not_null_enum_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\') NOT NULL DEFAULT \'1\'');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('status_flag', $columnDefinition->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnDataType());
        $this->assertEquals('enum', $columnDefinition->getMethodName());
        $this->assertCount(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals('1', $columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])->default(\'1\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_enum_with_spaces()
    {
        $columnTokenizer = ColumnTokenizer::parse('`calculate` enum(\'one\',\'and\',\'highest or\',\'lowest or\',\'sum\',\'highest position or\',\'lowest position or\') COLLATE utf8mb4_general_ci NOT NULL COMMENT \'set the way we calculate a feature value. with high or low or the sort is by position\'');
        $definition = $columnTokenizer->definition();

        $this->assertEquals('$table->enum(\'calculate\', [\'one\', \'and\', \'highest or\', \'lowest or\', \'sum\', \'highest position or\', \'lowest position or\'])->comment("set the way we calculate a feature value. with high or low or the sort is by position")', $definition->render());
    }

    public function test_it_tokenizes_enum_with_special_characters()
    {
        $columnTokenizer = ColumnTokenizer::parse('`calculate` enum(\'one\',\'and\',\'highest-or\',\'lowest^or\',\'sum%\',\'highest $ position or\',\'lowest+_<>?/ position or\',\'"quoted"\') COLLATE utf8mb4_general_ci NOT NULL COMMENT \'set the way we calculate a feature value. with high or low or the sort is by position\'');
        $definition = $columnTokenizer->definition();

        $this->assertEquals('$table->enum(\'calculate\', [\'one\', \'and\', \'highest-or\', \'lowest^or\', \'sum%\', \'highest $ position or\', \'lowest+_<>?/ position or\', \'"quoted"\'])->comment("set the way we calculate a feature value. with high or low or the sort is by position")', $definition->render());
    }

    //endregion

    //region POINT, MULTIPOINT
    public function test_it_tokenizes_point_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`point` point NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('point', $columnTokenizer->getColumnDataType());
        $this->assertEquals('point', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals('$table->point(\'point\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_multipoint_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`point` multipoint NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('multipoint', $columnTokenizer->getColumnDataType());
        $this->assertEquals('multiPoint', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals('$table->multiPoint(\'point\')', $columnDefinition->render());
    }

    //endregion

    //region POLYGON, MULTIPOLYGON
    public function test_it_tokenizes_polygon_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`polygon` polygon NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('polygon', $columnTokenizer->getColumnDataType());
        $this->assertEquals('polygon', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals('$table->polygon(\'polygon\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_multipolygon_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`polygon` multipolygon NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('multipolygon', $columnTokenizer->getColumnDataType());
        $this->assertEquals('multiPolygon', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals('$table->multiPolygon(\'polygon\')', $columnDefinition->render());
    }

    //endregion,

    //region GEOMETRY
    public function test_it_tokenizes_geometry_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`geometry` geometry NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('geometry', $columnTokenizer->getColumnDataType());
        $this->assertEquals('geometry', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
    }

    public function test_it_tokenizes_geometry_collection_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`geometry_collection` geometrycollection NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('geometrycollection', $columnTokenizer->getColumnDataType());
        $this->assertEquals('geometryCollection', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
    }

    //endregion

    //region SET
    public function test_it_tokenizes_set_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`set_field` set(\'1\',\'2\',\'3\') COLLATE utf8mb4_unicode_ci DEFAULT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('set', $columnTokenizer->getColumnDataType());
        $this->assertEquals('set', $columnDefinition->getMethodName());
        $this->assertCount(1, $columnDefinition->getMethodParameters());
        $this->assertNotNull($columnDefinition->getCollation());
        $this->assertTrue($columnDefinition->isNullable());

        $this->assertEquals('$table->set(\'set_field\', [\'1\', \'2\', \'3\'])->nullable()', $columnDefinition->render());
    }

    //endregion

    //region UUID
    public function test_it_tokenizes_uuid_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`uuid_col` char(36) COLLATE utf8mb4_unicode_ci NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('char', $columnTokenizer->getColumnDataType());
        $this->assertEquals('char', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNotNull($columnDefinition->getCollation());
        $this->assertFalse($columnDefinition->isNullable());

        $this->assertEquals('$table->uuid(\'uuid_col\')', $columnDefinition->render());
    }

    //endregion

    //region DATE, YEAR, TIME
    public function test_it_tokenizes_date_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`birth_date` date NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('date', $columnTokenizer->getColumnDataType());
        $this->assertEquals('date', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertFalse($columnDefinition->isNullable());

        $this->assertEquals('$table->date(\'birth_date\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_year_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`birth_year` year NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('year', $columnTokenizer->getColumnDataType());
        $this->assertEquals('year', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertFalse($columnDefinition->isNullable());

        $this->assertEquals('$table->year(\'birth_year\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_time_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`birth_time` time NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('time', $columnTokenizer->getColumnDataType());
        $this->assertEquals('time', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertFalse($columnDefinition->isNullable());

        $this->assertEquals('$table->time(\'birth_time\')', $columnDefinition->render());
    }

    //endregion

    //region LINESTRING, MULTILINESTRING
    public function test_it_tokenizes_linestring_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`str` linestring NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('linestring', $columnTokenizer->getColumnDataType());
        $this->assertEquals('lineString', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertFalse($columnDefinition->isNullable());

        $this->assertEquals('$table->lineString(\'str\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_multilinestring_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`str` multilinestring NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('multilinestring', $columnTokenizer->getColumnDataType());
        $this->assertEquals('multiLineString', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertFalse($columnDefinition->isNullable());

        $this->assertEquals('$table->multiLineString(\'str\')', $columnDefinition->render());
    }

    //endregion

    public function test_it_tokenizes_generated_as_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`total` decimal(24,6) GENERATED ALWAYS AS ((`quantity` * `unit_price`)) STORED');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('decimal', $columnTokenizer->getColumnDataType());
        $this->assertEquals('decimal', $columnDefinition->getMethodName());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertEquals('(`quantity` * `unit_price`)', $columnDefinition->getStoredAs());

        $this->assertEquals('$table->decimal(\'total\', 24, 6)->storedAs("(`quantity` * `unit_price`)")', $columnDefinition->render());
    }

    public function test_it_tokenizes_generated_as_column_example()
    {
        $columnTokenizer = ColumnTokenizer::parse('`full_name` varchar(150) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (concat(`first_name`,\' \',`last_name`)) STORED');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('varchar', $columnTokenizer->getColumnDataType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertEquals('(concat(`first_name`,\' \',`last_name`))', $columnDefinition->getStoredAs());

        $this->assertEquals('$table->string(\'full_name\', 150)->storedAs("(concat(`first_name`,\' \',`last_name`))")', $columnDefinition->render());
    }

    public function test_it_tokenizes_virtual_as_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`total` decimal(24,6) AS ((`quantity` * `unit_price`))');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('decimal', $columnTokenizer->getColumnDataType());
        $this->assertEquals('decimal', $columnDefinition->getMethodName());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertEquals('(`quantity` * `unit_price`)', $columnDefinition->getVirtualAs());

        $this->assertEquals('$table->decimal(\'total\', 24, 6)->virtualAs("(`quantity` * `unit_price`)")', $columnDefinition->render());
    }
}
