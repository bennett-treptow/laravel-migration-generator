<?php

namespace Tests\Unit\Tokenizers\MySQL;

use Tests\TestCase;
use LaravelMigrationGenerator\Generators\MySQL\TableGenerator;
use LaravelMigrationGenerator\Tokenizers\MySQL\IndexTokenizer;
use LaravelMigrationGenerator\Tokenizers\MySQL\ColumnTokenizer;

class ColumnTokenizerTest extends TestCase
{
    //region VARCHAR
    public function test_it_tokenizes_a_not_null_varchar_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('email', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
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
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertEquals(200, $columnDefinition->getMethodParameters()[0]);
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertEquals('$table->string(\'favorite_color\', 200)->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT \'orange\'');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertEquals('orange', $columnDefinition->getDefaultValue());
        $this->assertEquals('$table->string(\'favorite_color\')->nullable()->default(\'orange\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_null_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('favorite_color', $columnDefinition->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnDefinition->getCollation());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertEquals('$table->string(\'favorite_color\')->nullable()', $columnDefinition->render());
    }

    //endregion

    //region TEXT & Variants
    public function test_it_tokenizes_a_not_null_tinytext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` tinytext NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('tinytext', $columnTokenizer->getColumnType());
        $this->assertEquals('tinyText', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->tinyText(\'notes\')', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_tinytext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` tinytext');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('tinytext', $columnTokenizer->getColumnType());
        $this->assertEquals('tinyText', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->tinyText(\'notes\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_text_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` text NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('notes', $columnDefinition->getColumnName());
        $this->assertEquals('text', $columnTokenizer->getColumnType());
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
        $this->assertEquals('text', $columnTokenizer->getColumnType());
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
        $this->assertEquals('mediumtext', $columnTokenizer->getColumnType());
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
        $this->assertEquals('mediumtext', $columnTokenizer->getColumnType());
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
        $this->assertEquals('longtext', $columnTokenizer->getColumnType());
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
        $this->assertEquals('longtext', $columnTokenizer->getColumnType());
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
        $this->assertEquals('smallint', $columnTokenizer->getColumnType());
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
        $this->assertEquals('smallint', $columnTokenizer->getColumnType());
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
        $this->assertEquals('smallint', $columnTokenizer->getColumnType());
        $this->assertEquals('smallInteger', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->smallInteger(\'cats\')->unsigned()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_nullable_big_int_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`template_id` bigint(20) unsigned DEFAULT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('template_id', $columnDefinition->getColumnName());
        $this->assertEquals('bigint', $columnTokenizer->getColumnType());
        $this->assertEquals('bigInteger', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertEquals('$table->bigInteger(\'template_id\')->unsigned()->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_primary_auto_inc_int_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`id` int(9) unsigned NOT NULL AUTO_INCREMENT');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('id', $columnDefinition->getColumnName());
        $this->assertEquals('int', $columnTokenizer->getColumnType());
        $this->assertEquals('integer', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->increments(\'id\')', $columnDefinition->render());
    }

    //endregion

    //region FLOAT
    public function test_it_tokenizes_float_without_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float NOT NULL');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
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
        $this->assertEquals('float', $columnTokenizer->getColumnType());
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
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_null_with_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2)');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_without_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float NOT NULL DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
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
        $this->assertEquals('float', $columnTokenizer->getColumnType());
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
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertEquals(1.0, $columnDefinition->getDefaultValue());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\')->nullable()->default(1.0)', $columnDefinition->render());
    }

    public function test_it_tokenizes_float_null_with_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2) DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();
        $this->assertEquals('parameter', $columnDefinition->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertEquals(1.00, $columnDefinition->getDefaultValue());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)->nullable()->default(1.00)', $columnDefinition->render());
    }

    //endregion

    //region DECIMAL
    public function test_it_tokenizes_a_not_null_decimal_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnType());
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
        $this->assertEquals('decimal', $columnTokenizer->getColumnType());
        $this->assertEquals('decimal', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->decimal(\'amount\', 9, 2)->unsigned()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_decimal_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) NOT NULL DEFAULT 1.00');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnType());
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
        $this->assertEquals('decimal', $columnTokenizer->getColumnType());
        $this->assertEquals('decimal', $columnDefinition->getMethodName());
        $this->assertCount(2, $columnDefinition->getMethodParameters());
        $this->assertEquals(9, $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals(2, $columnDefinition->getMethodParameters()[1]);
        $this->assertEquals(1.0, $columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertTrue($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->decimal(\'amount\', 9, 2)->unsigned()->default(1.00)', $columnDefinition->render());
    }

    //endregion

    //region DOUBLE
    public function test_it_tokenizes_a_not_null_double_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('amount', $columnDefinition->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnType());
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
        $this->assertEquals('double', $columnTokenizer->getColumnType());
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
        $this->assertEquals('double', $columnTokenizer->getColumnType());
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
        $this->assertEquals('double', $columnTokenizer->getColumnType());
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
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
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
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
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
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
        $this->assertEquals('dateTime', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_null_default_value_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime DEFAULT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
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
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
        $this->assertEquals('dateTime', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->nullable()->useCurrent()', $columnDefinition->render());
    }

    //endregion

    //region TIMESTAMP
    public function test_it_tokenizes_a_not_null_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
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
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
        $this->assertEquals('timestamp', $columnDefinition->getMethodName());
        $this->assertCount(0, $columnDefinition->getMethodParameters());
        $this->assertNull($columnDefinition->getDefaultValue());
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertFalse($columnDefinition->isUnsigned());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_a_not_null_use_current_timestamp_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('sent_at', $columnDefinition->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
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
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
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
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
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
        $this->assertEquals('enum', $columnTokenizer->getColumnType());
        $this->assertEquals('enum', $columnDefinition->getMethodName());
        $this->assertCount(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnDefinition->getMethodParameters()[0]);
        $this->assertTrue($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])->nullable()', $columnDefinition->render());
    }

    public function test_it_tokenizes_not_null_enum_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\') NOT NULL');
        $columnDefinition = $columnTokenizer->definition();

        $this->assertEquals('status_flag', $columnDefinition->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnType());
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
        $this->assertEquals('enum', $columnTokenizer->getColumnType());
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
        $this->assertEquals('enum', $columnTokenizer->getColumnType());
        $this->assertEquals('enum', $columnDefinition->getMethodName());
        $this->assertCount(4, $columnDefinition->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnDefinition->getMethodParameters()[0]);
        $this->assertEquals('1', $columnDefinition->getDefaultValue());
        $this->assertFalse($columnDefinition->isNullable());
        $this->assertNull($columnDefinition->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])->default(\'1\')', $columnDefinition->render());
    }

    //endregion

    public function test_adding_index_to_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`user_id` int(10) unsigned NOT NULL');
        $columnDefinition = $columnTokenizer->definition();
        $indexTokenizer = IndexTokenizer::parse('KEY `fk_user_id_idx` (`user_id`)');
        $columnTokenizer->index($indexTokenizer);

        $table = new TableGenerator('test', [
            '`user_id` int(10) unsigned NOT NULL',
            'KEY `fk_user_id_idx` (`user_id`)'
        ]);

        $columnTokenizer->finalPass($table);

        $this->assertEquals('$table->integer(\'user_id\')->unsigned()->index()', $columnDefinition->render());
    }
}
