<?php

declare(strict_types=1);

namespace App\Storage;

use App\Dto\TransactionData;

interface TransactionStorageInterface
{
    /**
     * @return TransactionData[]
     */
    public function getDataByDsn(string $dsn): array;
}
