<?php

namespace Tests\Unit\Tokenizers\MySQL;

use Tests\TestCase;
use LaravelMigrationGenerator\Generators\MySQLTableGenerator;
use LaravelMigrationGenerator\Tokenizers\MySQL\IndexTokenizer;
use LaravelMigrationGenerator\Tokenizers\MySQL\ColumnTokenizer;

class ColumnTokenizerTest extends TestCase
{
    //region Boolean
    public function test_it_tokenizes_a_small_integer_to_boolean()
    {
        $columnTokenizer = ColumnTokenizer::parse('`has_cats` tinyint(1) NOT NULL DEFAULT 0');

        $this->assertEquals('has_cats', $columnTokenizer->getColumnName());
        $this->assertEquals('tinyint', $columnTokenizer->getColumnType());
        $this->assertEquals('tinyInteger', $columnTokenizer->getMethod());
        $this->assertCount(1, $columnTokenizer->getMethodParameters());
        $this->assertEquals(1, $columnTokenizer->getMethodParameters()[0]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->boolean(\'has_cats\')->default(0)', $columnTokenizer->toMethod());
    }

    //endregion

    //region VARCHAR
    public function test_it_tokenizes_a_not_null_varchar_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');

        $this->assertEquals('email', $columnTokenizer->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnTokenizer->getMethod());
        $this->assertEquals(255, $columnTokenizer->getMethodParameters()[0]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnTokenizer->getCollation());
        $this->assertEquals('$table->string(\'email\', 255)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_varchar_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci');

        $this->assertEquals('favorite_color', $columnTokenizer->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnTokenizer->getMethod());
        $this->assertEquals(255, $columnTokenizer->getMethodParameters()[0]);
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnTokenizer->getCollation());
        $this->assertEquals('$table->string(\'favorite_color\', 255)->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT \'orange\'');

        $this->assertEquals('favorite_color', $columnTokenizer->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnTokenizer->getMethod());
        $this->assertEquals(255, $columnTokenizer->getMethodParameters()[0]);
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnTokenizer->getCollation());
        $this->assertEquals('orange', $columnTokenizer->getDefaultValue());
        $this->assertEquals('$table->string(\'favorite_color\', 255)->nullable()->default(\'orange\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_null_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL');

        $this->assertEquals('favorite_color', $columnTokenizer->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnTokenizer->getMethod());
        $this->assertEquals(255, $columnTokenizer->getMethodParameters()[0]);
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnTokenizer->getCollation());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertEquals('$table->string(\'favorite_color\', 255)->nullable()', $columnTokenizer->toMethod());
    }

    //endregion

    //region TEXT & Variants
    public function test_it_tokenizes_a_not_null_tinytext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` tinytext NOT NULL');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('tinytext', $columnTokenizer->getColumnType());
        $this->assertEquals('tinyText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->tinyText(\'notes\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_tinytext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` tinytext');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('tinytext', $columnTokenizer->getColumnType());
        $this->assertEquals('tinyText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->tinyText(\'notes\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_text_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` text NOT NULL');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('text', $columnTokenizer->getColumnType());
        $this->assertEquals('text', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->text(\'notes\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_text_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` text');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('text', $columnTokenizer->getColumnType());
        $this->assertEquals('text', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->text(\'notes\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_mediumtext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` mediumtext NOT NULL');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('mediumtext', $columnTokenizer->getColumnType());
        $this->assertEquals('mediumText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->mediumText(\'notes\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_mediumtext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` mediumtext');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('mediumtext', $columnTokenizer->getColumnType());
        $this->assertEquals('mediumText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->mediumText(\'notes\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_longtext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` longtext NOT NULL');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('longtext', $columnTokenizer->getColumnType());
        $this->assertEquals('longText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->longText(\'notes\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_longtext_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`notes` longtext');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('longtext', $columnTokenizer->getColumnType());
        $this->assertEquals('longText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->longText(\'notes\')->nullable()', $columnTokenizer->toMethod());
    }

    //endregion

    //region INT & Variants
    public function test_it_tokenizes_a_not_null_smallint_without_param_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`cats` smallint NOT NULL');

        $this->assertEquals('cats', $columnTokenizer->getColumnName());
        $this->assertEquals('smallint', $columnTokenizer->getColumnType());
        $this->assertEquals('smallInteger', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->smallInteger(\'cats\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_smallint_with_param_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`cats` smallint(6) NOT NULL');

        $this->assertEquals('cats', $columnTokenizer->getColumnName());
        $this->assertEquals('smallint', $columnTokenizer->getColumnType());
        $this->assertEquals('smallInteger', $columnTokenizer->getMethod());
        $this->assertCount(1, $columnTokenizer->getMethodParameters());
        $this->assertEquals(6, $columnTokenizer->getMethodParameters()[0]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->smallInteger(\'cats\', 6)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_unsigned_smallint_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`cats` smallint(6) unsigned NOT NULL');

        $this->assertEquals('cats', $columnTokenizer->getColumnName());
        $this->assertEquals('smallint', $columnTokenizer->getColumnType());
        $this->assertEquals('smallInteger', $columnTokenizer->getMethod());
        $this->assertCount(1, $columnTokenizer->getMethodParameters());
        $this->assertEquals(6, $columnTokenizer->getMethodParameters()[0]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertTrue($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->unsignedSmallInteger(\'cats\', 6)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_primary_auto_inc_int_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`id` int(9) unsigned NOT NULL AUTO_INCREMENT');

        $this->assertEquals('id', $columnTokenizer->getColumnName());
        $this->assertEquals('int', $columnTokenizer->getColumnType());
        $this->assertEquals('integer', $columnTokenizer->getMethod());
        $this->assertCount(1, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertTrue($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->increments(\'id\')', $columnTokenizer->toMethod());
    }

    //endregion

    //region FLOAT
    public function test_it_tokenizes_float_without_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float NOT NULL');
        $this->assertEquals('parameter', $columnTokenizer->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->float(\'parameter\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_float_with_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2) NOT NULL');
        $this->assertEquals('parameter', $columnTokenizer->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(4, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_float_null_without_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float');
        $this->assertEquals('parameter', $columnTokenizer->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->float(\'parameter\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_float_null_with_params_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2)');
        $this->assertEquals('parameter', $columnTokenizer->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(4, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_float_without_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float NOT NULL DEFAULT 1.00');
        $this->assertEquals('parameter', $columnTokenizer->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertEquals(1.0, $columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->float(\'parameter\')->default(1.00)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_float_with_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2) NOT NULL DEFAULT 1.00');
        $this->assertEquals('parameter', $columnTokenizer->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(4, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertEquals(1.00, $columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)->default(1.00)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_float_null_without_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float DEFAULT 1.0');
        $this->assertEquals('parameter', $columnTokenizer->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertEquals(1.0, $columnTokenizer->getDefaultValue());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->float(\'parameter\')->nullable()->default(1.0)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_float_null_with_params_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`parameter` float(4,2) DEFAULT 1.00');
        $this->assertEquals('parameter', $columnTokenizer->getColumnName());
        $this->assertEquals('float', $columnTokenizer->getColumnType());
        $this->assertEquals('float', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(4, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertEquals(1.00, $columnTokenizer->getDefaultValue());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->float(\'parameter\', 4, 2)->nullable()->default(1.00)', $columnTokenizer->toMethod());
    }

    //endregion

    //region DECIMAL
    public function test_it_tokenizes_a_not_null_decimal_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) NOT NULL');

        $this->assertEquals('amount', $columnTokenizer->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnType());
        $this->assertEquals('decimal', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->decimal(\'amount\', 9, 2)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_unsigned_decimal_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) unsigned NOT NULL');

        $this->assertEquals('amount', $columnTokenizer->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnType());
        $this->assertEquals('decimal', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertTrue($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->unsignedDecimal(\'amount\', 9, 2)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_decimal_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) NOT NULL DEFAULT 1.00');

        $this->assertEquals('amount', $columnTokenizer->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnType());
        $this->assertEquals('decimal', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals(1.0, $columnTokenizer->getDefaultValue());
        $this->assertEquals('$table->decimal(\'amount\', 9, 2)->default(1.00)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_unsigned_decimal_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` decimal(9,2) unsigned NOT NULL DEFAULT 1.00');

        $this->assertEquals('amount', $columnTokenizer->getColumnName());
        $this->assertEquals('decimal', $columnTokenizer->getColumnType());
        $this->assertEquals('decimal', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertEquals(1.0, $columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertTrue($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->unsignedDecimal(\'amount\', 9, 2)->default(1.00)', $columnTokenizer->toMethod());
    }

    //endregion

    //region DOUBLE
    public function test_it_tokenizes_a_not_null_double_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) NOT NULL');

        $this->assertEquals('amount', $columnTokenizer->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnType());
        $this->assertEquals('double', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->double(\'amount\', 9, 2)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_unsigned_double_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) unsigned NOT NULL');

        $this->assertEquals('amount', $columnTokenizer->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnType());
        $this->assertEquals('double', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertTrue($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->unsignedDouble(\'amount\', 9, 2)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_double_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) NOT NULL DEFAULT 1.00');

        $this->assertEquals('amount', $columnTokenizer->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnType());
        $this->assertEquals('double', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals(1.00, $columnTokenizer->getDefaultValue());
        $this->assertEquals('$table->double(\'amount\', 9, 2)->default(1.00)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_unsigned_double_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`amount` double(9,2) unsigned NOT NULL DEFAULT 1.00');

        $this->assertEquals('amount', $columnTokenizer->getColumnName());
        $this->assertEquals('double', $columnTokenizer->getColumnType());
        $this->assertEquals('double', $columnTokenizer->getMethod());
        $this->assertCount(2, $columnTokenizer->getMethodParameters());
        $this->assertEquals(9, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals(2, $columnTokenizer->getMethodParameters()[1]);
        $this->assertEquals(1.00, $columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertTrue($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->unsignedDouble(\'amount\', 9, 2)->default(1.00)', $columnTokenizer->toMethod());
    }

    //endregion

    //region DATETIME
    public function test_it_tokenizes_a_not_null_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime NOT NULL');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
        $this->assertEquals('dateTime', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_datetime_default_now_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
        $this->assertEquals('dateTime', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->useCurrent()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
        $this->assertEquals('dateTime', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_default_value_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime DEFAULT NULL');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
        $this->assertEquals('dateTime', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_default_value_now_datetime_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` datetime DEFAULT CURRENT_TIMESTAMP');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('datetime', $columnTokenizer->getColumnType());
        $this->assertEquals('dateTime', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->dateTime(\'sent_at\')->nullable()->useCurrent()', $columnTokenizer->toMethod());
    }

    //endregion

    //region TIMESTAMP
    public function test_it_tokenizes_a_not_null_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NOT NULL');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
        $this->assertEquals('timestamp', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
        $this->assertEquals('timestamp', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_use_current_timestamp_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
        $this->assertEquals('timestamp', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')->useCurrent()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_default_value_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NOT NULL DEFAULT \'2000-01-01 00:00:01\'');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
        $this->assertEquals('timestamp', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertEquals('2000-01-01 00:00:01', $columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')->default(\'2000-01-01 00:00:01\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_default_value_timestamp_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`sent_at` timestamp NULL DEFAULT \'2000-01-01 00:00:01\'');

        $this->assertEquals('sent_at', $columnTokenizer->getColumnName());
        $this->assertEquals('timestamp', $columnTokenizer->getColumnType());
        $this->assertEquals('timestamp', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertEquals('2000-01-01 00:00:01', $columnTokenizer->getDefaultValue());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertFalse($columnTokenizer->getUnsigned());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->timestamp(\'sent_at\')->nullable()->default(\'2000-01-01 00:00:01\')', $columnTokenizer->toMethod());
    }

    //endregion

    //region ENUM
    public function test_it_tokenizes_enum_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\')');

        $this->assertEquals('status_flag', $columnTokenizer->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnType());
        $this->assertEquals('enum', $columnTokenizer->getMethod());
        $this->assertCount(4, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnTokenizer->getMethodParameters()[0]);
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_not_null_enum_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\') NOT NULL');

        $this->assertEquals('status_flag', $columnTokenizer->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnType());
        $this->assertEquals('enum', $columnTokenizer->getMethod());
        $this->assertCount(4, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnTokenizer->getMethodParameters()[0]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_enum_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\') DEFAULT NULL');

        $this->assertEquals('status_flag', $columnTokenizer->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnType());
        $this->assertEquals('enum', $columnTokenizer->getMethod());
        $this->assertCount(4, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnTokenizer->getMethodParameters()[0]);
        $this->assertNull($columnTokenizer->getDefaultValue());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_not_null_enum_with_default_value_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`status_flag` enum(\'1\',\'2\',\'3\',\'4\') NOT NULL DEFAULT \'1\'');

        $this->assertEquals('status_flag', $columnTokenizer->getColumnName());
        $this->assertEquals('enum', $columnTokenizer->getColumnType());
        $this->assertEquals('enum', $columnTokenizer->getMethod());
        $this->assertCount(4, $columnTokenizer->getMethodParameters()[0]);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $columnTokenizer->getMethodParameters()[0]);
        $this->assertEquals('1', $columnTokenizer->getDefaultValue());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->enum(\'status_flag\', [\'1\', \'2\', \'3\', \'4\'])->default(\'1\')', $columnTokenizer->toMethod());
    }

    //endregion

    public function test_adding_index_to_column()
    {
        $columnTokenizer = ColumnTokenizer::parse('`user_id` int(10) unsigned NOT NULL');
        $indexTokenizer = IndexTokenizer::parse('KEY `fk_user_id_idx` (`user_id`)');
        $columnTokenizer->index($indexTokenizer);

        $table = new MySQLTableGenerator('test', [
            '`user_id` int(10) unsigned NOT NULL',
            'KEY `fk_user_id_idx` (`user_id`)'
        ]);

        $columnTokenizer->finalPass($table);

        $this->assertEquals('$table->unsignedInteger(\'user_id\', 10)->index()', $columnTokenizer->toMethod());
    }
}
