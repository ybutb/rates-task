<?php

declare(strict_types=1);

namespace App\Service\BinProvider;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DefaultBinProvider implements BinProviderInterface
{
    public function __construct(private readonly HttpClientInterface $binClient)
    {
    }

    public function getData(string $bin): string
    {
        $response = $this->binClient->request('GET', '/' . $bin);

        $binData = json_decode($response->getContent(), true);

        if (empty($binData['country']['alpha2'])) {
            throw new \Exception('No data regarding card issuer country.'); // remove
        }

        return $binData['country']['alpha2'];
    }
}
