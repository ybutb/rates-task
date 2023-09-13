<?php

declare(strict_types=1);

namespace App\Storage;

use RuntimeException;
use SplFileObject;
use App\Dto\TransactionData;

class TransactionStorage
{

    public function __construct(private readonly string $dataFolder)
    {
    }

    /**
     * @return TransactionData[]
     */
    public function getDataByDsn(string $dsn): array
    {
        $pathToFile = $this->dataFolder . '/' . $dsn;

        if (!file_exists($pathToFile)) {
            throw new RuntimeException('Input file does not exist.');
        }

        $file = new SplFileObject($pathToFile);
        $result = [];

        while (!$file->eof()) {
            $filteredJson = str_replace("\n", '', $file->fgets());
            $accountDecoded = json_decode($filteredJson, true);

            if (!is_array($accountDecoded)) {
                throw new RuntimeException('Wrong format data in the file.');
            }

            $result[] = new TransactionData(
                $accountDecoded['bin'],
                $accountDecoded['currency'],
                $accountDecoded['amount']
            );
        }

        return $result;
    }
}
