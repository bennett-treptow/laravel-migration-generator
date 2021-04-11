<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

trait WritesToFile
{
    public function write(string $basePath, string $tabCharacter = '    '): void
    {
        if (! $this->isWritable()) {
            return;
        }

        $stubPath = $this->getStubPath();
        $stub = $this->generateStub($stubPath, $tabCharacter);

        $fileName = $this->getStubFileName();
        file_put_contents($basePath . '/' . $fileName, $stub);
    }
}
