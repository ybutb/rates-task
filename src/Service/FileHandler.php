<?php

declare(strict_types=1);

namespace App\Service;

use LogicException;
use RuntimeException;
use SplFileObject;

class FileHandler
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @throws RuntimeException Failed to open file.
     * @throws LogicException When the path leads to directory.
     */
    public function readByLine(string $path): iterable
    {
        $file = new SplFileObject($path);

        while (!$file->eof()) {
            yield $file->fgets();
        }
    }
}