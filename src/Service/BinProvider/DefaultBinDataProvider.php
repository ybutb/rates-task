<?php

declare(strict_types=1);

namespace App\Service\BinProvider;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DefaultBinDataProvider implements BinDataProviderInterface
{
    public function __construct(private readonly HttpClientInterface $binClient)
    {
    }

    public function getCountryCodeByBin(string $bin): string
    {
        $this->validateBin($bin);

        $response = $this->binClient->request('GET', '/' . $bin);

        try {
            $responseContent = $response->getContent();
        } catch (ExceptionInterface $exception) {
            throw new RuntimeException('Bin provider API request failed. Error: ' . $exception->getMessage());
        }

        $binData = json_decode($responseContent, true);

        if (empty($binData['country']['alpha2'])) {
            throw new RuntimeException('No card issuer country data.');
        }

        return $binData['country']['alpha2'];
    }

    public function validateBin(string $bin): void
    {
        if (!preg_match('/^[0-9]{6,}$/', $bin)) {
            throw new RuntimeException('Bin is not valid.');
            // It's not a good idea to add a bin into exception message as it's a sensitive data.
            // In this case it's better to use a transaction id but in the provided input.txt there is no such field.
        }
    }
}
