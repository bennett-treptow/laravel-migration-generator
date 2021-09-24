<?php

namespace Tests\Unit;

use Tests\TestCase;
use LaravelMigrationGenerator\Helpers\Formatter;

class FormatterTest extends TestCase
{
    public function test_can_format_single_line()
    {
        $formatter = new Formatter();
        $formatter->line('Test');
        $this->assertEquals('Test', $formatter->render());
    }

    public function test_can_chain()
    {
        $formatter = new Formatter();
        $line = $formatter->line('$this->call(function(){');
        $line('$this->die();');
        $formatter->line('});');
        $this->assertEquals(<<<STR
        \$this->call(function(){
            \$this->die();
        });
        STR, $formatter->render());
    }

    public function test_can_get_current_line_indent_level()
    {
        $formatter = new Formatter();
        $formatter->line('Line');
        $formatter->line('Line 2');

        $body = <<<STR
    [Test]
STR;

        $replaced = $formatter->replaceOnLine('[Test]', $body);
        $shouldEqual = <<<STR
    Line
    Line 2
STR;
        $this->assertEquals($shouldEqual, $replaced);
    }

    public function test_can_replace_on_no_indent()
    {
        $replaced = Formatter::replace('    ', '[TEST]', 'Test', '[TEST]');
        $this->assertEquals('Test', $replaced);
    }
}
