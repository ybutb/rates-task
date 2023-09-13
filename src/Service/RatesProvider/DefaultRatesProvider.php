<?php

declare(strict_types=1);

namespace App\Service\RatesProvider;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Dto\TransactionData;

class DefaultRatesProvider implements RatesProviderInterface
{
    public function __construct(
        private readonly HttpClientInterface $ratesClient,
        private readonly string $apiKey,
    ) {
    }

    public function getData(TransactionData $accountData): float
    {
        $response = $this->ratesClient->request('GET', '', [
            'query' => ['access_key' => $this->apiKey],
        ]);

        $ratesData = json_decode($response->getContent(), true);
        $rate = $ratesData['rates'][$accountData->getCurrency()] ?? null;

        if (!$rate) {
            throw new RuntimeException('Currency was not found'); // remove
        }

        return $rate;
    }
}
