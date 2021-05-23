<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

trait WritesToFile
{
    public function write(string $basePath, string $tabCharacter = '    '): void
    {
        if (method_exists($this, 'isWritable') && ! $this->isWritable()) {
            return;
        }

        $stub = $this->generateStub($tabCharacter);

        $fileName = $this->getStubFileName();
        file_put_contents($basePath . '/' . $fileName, $stub);
    }
}
