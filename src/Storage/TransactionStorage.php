<?php

declare(strict_types=1);

namespace App\Storage;

use App\Service\FileHandler;
use RuntimeException;
use App\Dto\TransactionData;

final class TransactionStorage implements TransactionStorageInterface
{

    public function __construct(
        private readonly FileHandler $fileHandler,
        private readonly string $dataFolder
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getDataByDsn(string $dsn): array
    {
        $pathToFile = $this->dataFolder . '/' . $dsn;

        if (!$this->fileHandler->exists($pathToFile)) {
            throw new RuntimeException('Input file does not exist.');
        }

        $result = [];

        foreach ($this->fileHandler->readByLine($pathToFile) as $row) {
            $filteredJson = str_replace("\n", '', $row);
            $accountDecoded = json_decode($filteredJson, true);

            $this->validateData($accountDecoded);

            $result[] = new TransactionData(
                $accountDecoded['bin'],
                $accountDecoded['currency'],
                $accountDecoded['amount']
            );
        }

        return $result;
    }

    /**
     * @throws RuntimeException If file contains invalid data
     */
    private function validateData($accountDataDecoded): void
    {
        if (!is_array($accountDataDecoded) || !isset($accountDataDecoded['bin'], $accountDataDecoded['currency'], $accountDataDecoded['amount'])) {
            throw new RuntimeException('Wrong format data in the file.');
        }
    }
}
