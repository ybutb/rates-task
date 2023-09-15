<?php

declare(strict_types=1);

namespace App\Service;

use SplFileObject;

class FileHandler
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }
    public function readByLine(string $path): iterable
    {
        $file = new SplFileObject($path);

        while (!$file->eof()) {
            yield $file->fgets();
        }
    }
}