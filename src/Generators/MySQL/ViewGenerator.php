<?php

namespace LaravelMigrationGenerator\Generators\MySQL;

use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\Generators\BaseViewGenerator;
use LaravelMigrationGenerator\Generators\Interfaces\ViewGeneratorInterface;

class ViewGenerator extends BaseViewGenerator implements ViewGeneratorInterface
{
    public static function driver(): string
    {
        return 'mysql';
    }

    public function resolveSchema()
    {
        $structure = DB::select('SHOW CREATE VIEW `' . $this->viewName . '`');
        $structure = $structure[0];
        $structure = (array) $structure;
        if (isset($structure['Create View'])) {
            $this->schema = $structure['Create View'];
        }
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
}
