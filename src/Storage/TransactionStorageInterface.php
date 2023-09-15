<?php

declare(strict_types=1);

namespace App\Storage;

use App\Dto\TransactionData;
use RuntimeException;

interface TransactionStorageInterface
{
    /**
     * @return TransactionData[]
     * @throws RuntimeException Failed to retrieve data.
     */
    public function getDataByDsn(string $dsn): array;
}
