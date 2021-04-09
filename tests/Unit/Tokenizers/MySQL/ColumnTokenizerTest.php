<?php
namespace Tests\Unit\Tokenizers\MySQL;

use LaravelMigrationGenerator\Tokenizers\MySQL\ColumnTokenizer;
use Tests\TestCase;

class ColumnTokenizerTest extends TestCase {
    public function test_it_tokenizes_a_not_null_varchar_column(){
        $columnTokenizer = ColumnTokenizer::parse('`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL');

        $this->assertEquals('email', $columnTokenizer->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnTokenizer->getMethod());
        $this->assertEquals(255, $columnTokenizer->getMethodParameters()[0]);
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnTokenizer->getCollation());
        $this->assertEquals('$table->string(\'email\', 255)', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_varchar_column(){
        $columnTokenizer = ColumnTokenizer::parse('`favorite_color` varchar(255) COLLATE utf8mb4_unicode_ci');

        $this->assertEquals('favorite_color', $columnTokenizer->getColumnName());
        $this->assertEquals('varchar', $columnTokenizer->getColumnType());
        $this->assertEquals('string', $columnTokenizer->getMethod());
        $this->assertEquals(255, $columnTokenizer->getMethodParameters()[0]);
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertEquals('utf8mb4_unicode_ci', $columnTokenizer->getCollation());
        $this->assertEquals('$table->string(\'favorite_color\', 255)->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_varchar_default_value_column(){
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

    public function test_it_tokenizes_a_null_varchar_default_value_null_column(){
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

    public function test_it_tokenizes_a_not_null_tinytext_column(){
        $columnTokenizer = ColumnTokenizer::parse('`notes` tinytext NOT NULL');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('tinytext', $columnTokenizer->getColumnType());
        $this->assertEquals('tinyText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->tinyText(\'notes\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_tinytext_column(){
        $columnTokenizer = ColumnTokenizer::parse('`notes` tinytext');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('tinytext', $columnTokenizer->getColumnType());
        $this->assertEquals('tinyText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->tinyText(\'notes\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_text_column(){
        $columnTokenizer = ColumnTokenizer::parse('`notes` text NOT NULL');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('text', $columnTokenizer->getColumnType());
        $this->assertEquals('text', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->text(\'notes\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_text_column(){
        $columnTokenizer = ColumnTokenizer::parse('`notes` text');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('text', $columnTokenizer->getColumnType());
        $this->assertEquals('text', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->text(\'notes\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_mediumtext_column(){
        $columnTokenizer = ColumnTokenizer::parse('`notes` mediumtext NOT NULL');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('mediumtext', $columnTokenizer->getColumnType());
        $this->assertEquals('mediumText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->mediumText(\'notes\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_mediumtext_column(){
        $columnTokenizer = ColumnTokenizer::parse('`notes` mediumtext');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('mediumtext', $columnTokenizer->getColumnType());
        $this->assertEquals('mediumText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->mediumText(\'notes\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_longtext_column(){
        $columnTokenizer = ColumnTokenizer::parse('`notes` longtext NOT NULL');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('longtext', $columnTokenizer->getColumnType());
        $this->assertEquals('longText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->longText(\'notes\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_null_longtext_column(){
        $columnTokenizer = ColumnTokenizer::parse('`notes` longtext');

        $this->assertEquals('notes', $columnTokenizer->getColumnName());
        $this->assertEquals('longtext', $columnTokenizer->getColumnType());
        $this->assertEquals('longText', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertTrue($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->longText(\'notes\')->nullable()', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_smallint_without_param_column(){
        $columnTokenizer = ColumnTokenizer::parse('`cats` smallint NOT NULL');

        $this->assertEquals('cats', $columnTokenizer->getColumnName());
        $this->assertEquals('smallint', $columnTokenizer->getColumnType());
        $this->assertEquals('smallInteger', $columnTokenizer->getMethod());
        $this->assertCount(0, $columnTokenizer->getMethodParameters());
        $this->assertFalse($columnTokenizer->getNullable());
        $this->assertNull($columnTokenizer->getCollation());
        $this->assertEquals('$table->smallInteger(\'cats\')', $columnTokenizer->toMethod());
    }

    public function test_it_tokenizes_a_not_null_smallint_with_param_column(){
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

    public function test_it_tokenizes_a_not_null_unsigned_smallint_column(){
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

    public function test_it_tokenizes_a_not_null_decimal_column(){
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

    public function test_it_tokenizes_a_not_null_unsigned_decimal_column(){
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

    public function test_it_tokenizes_a_not_null_decimal_with_default_value_column(){
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

    public function test_it_tokenizes_a_not_null_unsigned_decimal_with_default_value_column(){
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
}