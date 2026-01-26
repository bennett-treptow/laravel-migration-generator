<?php

namespace Tests\Unit;

use LaravelMigrationGenerator\Helpers\ValueToString;
use Tests\TestCase;

class ValueToStringTest extends TestCase
{
    //region Basic Functionality
    public function test_it_returns_null_for_null_value()
    {
        $this->assertEquals('null', ValueToString::make(null));
    }

    public function test_it_returns_integer_as_is()
    {
        $this->assertEquals(42, ValueToString::make(42));
    }

    public function test_it_returns_float_as_is()
    {
        $this->assertEquals(3.14, ValueToString::make(3.14));
    }

    public function test_it_quotes_string_value()
    {
        $this->assertEquals("'hello'", ValueToString::make('hello'));
    }

    public function test_it_uses_double_quotes_when_specified()
    {
        $this->assertEquals('"hello"', ValueToString::make('hello', false, false));
    }

    public function test_it_returns_array_as_bracketed_list()
    {
        $this->assertEquals("['one', 'two', 'three']", ValueToString::make(['one', 'two', 'three']));
    }

    public function test_it_singles_out_array_when_option_is_true()
    {
        $this->assertEquals("'single'", ValueToString::make(['single'], true));
    }

    public function test_it_does_not_single_out_multi_element_array()
    {
        $this->assertEquals("['one', 'two']", ValueToString::make(['one', 'two'], true));
    }

    //endregion

    //region Escape Functionality
    public function test_escape_escapes_single_quotes_by_default()
    {
        $this->assertEquals("test\\'s value", ValueToString::escape("test's value"));
    }

    public function test_escape_escapes_double_quotes_when_specified()
    {
        $this->assertEquals('test\\"s value', ValueToString::escape('test"s value', false));
    }

    public function test_escape_escapes_backslashes()
    {
        $this->assertEquals('path\\\\to\\\\file', ValueToString::escape('path\\to\\file'));
    }

    public function test_escape_escapes_both_backslashes_and_single_quotes()
    {
        $this->assertEquals("it\\'s a \\\\path", ValueToString::escape("it's a \\path"));
    }

    public function test_escape_escapes_both_backslashes_and_double_quotes()
    {
        $this->assertEquals('it\\"s a \\\\path', ValueToString::escape('it"s a \\path', false));
    }

    public function test_escape_handles_empty_string()
    {
        $this->assertEquals('', ValueToString::escape(''));
    }

    //endregion

    //region Security Tests - PHP Injection Prevention
    public function test_make_escapes_single_quotes_in_string()
    {
        $malicious = "test'); phpinfo();//";
        $result = ValueToString::make($malicious);

        // The result should have escaped quotes
        $this->assertEquals("'test\\'); phpinfo();//'", $result);

        // Verify it doesn't break out of string context
        $this->assertStringNotContainsString("''", $result);
    }

    public function test_make_escapes_single_quotes_in_array()
    {
        $malicious = ["col'); phpinfo();//"];
        $result = ValueToString::make($malicious);

        $this->assertEquals("['col\\'); phpinfo();//']", $result);
    }

    public function test_make_escapes_single_quotes_in_singled_out_array()
    {
        $malicious = ["col'); phpinfo();//"];
        $result = ValueToString::make($malicious, true);

        $this->assertEquals("'col\\'); phpinfo();//'", $result);
    }

    public function test_make_escapes_backslashes_followed_by_quotes()
    {
        // Input: test\' (backslash followed by single quote)
        // Output: 'test\\\'' - outer quotes, then \\ for backslash, \' for quote
        $value = "test\\'";
        $result = ValueToString::make($value);

        // 10 characters total: ' t e s t \\ \\ \' '
        $this->assertEquals(10, strlen($result));
        $this->assertEquals("'test\\\\\\''", $result);
    }

    public function test_make_escapes_complex_injection_attempt()
    {
        $malicious = "idx\\'; system('whoami');//";
        $result = ValueToString::make($malicious);

        // Backslashes and quotes should be escaped
        $this->assertEquals("'idx\\\\\\'; system(\\'whoami\\');//'", $result);
    }

    public function test_make_escapes_multiple_quotes_in_array()
    {
        $malicious = ["col'1", "col'2"];
        $result = ValueToString::make($malicious);

        $this->assertEquals("['col\\'1', 'col\\'2']", $result);
    }

    //endregion

    //region Cast Value Tests
    public function test_cast_float_creates_casted_value()
    {
        $result = ValueToString::castFloat('3.14');
        $this->assertEquals('float$:3.14', $result);
    }

    public function test_cast_binary_creates_casted_value()
    {
        $result = ValueToString::castBinary('0101');
        $this->assertEquals('binary$:0101', $result);
    }

    public function test_is_casted_value_detects_float()
    {
        $this->assertTrue(ValueToString::isCastedValue('float$:3.14'));
    }

    public function test_is_casted_value_detects_binary()
    {
        $this->assertTrue(ValueToString::isCastedValue('binary$:0101'));
    }

    public function test_is_casted_value_returns_false_for_regular_string()
    {
        $this->assertFalse(ValueToString::isCastedValue('regular string'));
    }

    public function test_make_handles_casted_float()
    {
        $casted = ValueToString::castFloat('3.14');
        $result = ValueToString::make($casted);

        $this->assertEquals('3.14', $result);
    }

    public function test_make_handles_casted_binary()
    {
        $casted = ValueToString::castBinary('0101');
        $result = ValueToString::make($casted);

        $this->assertEquals("b'0101'", $result);
    }

    //endregion
}
