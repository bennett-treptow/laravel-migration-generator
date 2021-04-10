<?php

namespace LaravelMigrationGenerator\Generators\MySQL;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\Generators\Interfaces\ViewGeneratorInterface;

class ViewGenerator implements ViewGeneratorInterface
{
    protected string $viewName;

    protected string $schema;

    public function __construct(string $viewName)
    {
        $this->viewName = $viewName;

        $structure = DB::select('SHOW CREATE VIEW `' . $viewName . '`');
        $structure = $structure[0];
        $structure = (array) $structure;
        if (isset($structure['Create View'])) {
            $this->schema = $structure['Create View'];
        }
    }

    public static function init(string $viewName)
    {
        $obj = new static($viewName);
        $obj->parse();

        return $obj;
    }

    public function parse()
    {
        if (preg_match('/CREATE(.*?)VIEW/', $this->schema, $matches)) {
            $this->schema = str_replace($matches[1], ' ', $this->schema);
        }

        if (preg_match_all('/isnull\((.+?)\)/', $this->schema, $matches)) {
            foreach ($matches[0] as $key => $match) {
                $this->schema = str_replace($match, $matches[1][$key] . ' IS NULL', $this->schema);
            }
        }
        if (preg_match('/collate utf8mb4_unicode_ci/', $this->schema)) {
            $this->schema = str_replace('collate utf8mb4_unicode_ci', '', $this->schema);
        }
    }

    public function write(string $basePath, string $tabCharacter = '    ')
    {
        $tab = str_repeat($tabCharacter, 3);
        $stub = file_get_contents(__DIR__ . '/../../Stubs/ViewStub.stub');
        $stub = str_replace('[ViewName]', Str::studly($this->viewName), $stub);
        $stub = str_replace('[View]', $this->viewName, $stub);
        $stub = str_replace('[Schema]', $tab . $this->schema, $stub);
        file_put_contents($basePath . '/0000_00_00_000000_create_test_' . $this->viewName . '_view.php', $stub);
    }
}
