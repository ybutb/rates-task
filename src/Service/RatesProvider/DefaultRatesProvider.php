<?php

declare(strict_types=1);

namespace App\Service\RatesProvider;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Dto\TransactionData;

final class DefaultRatesProvider implements RatesProviderInterface
{
    public function __construct(private readonly HttpClientInterface $ratesClient)
    {
    }

    /**
     * @inheritDoc
     */
    public function getRate(TransactionData $transactionData): float
    {
        $response = $this->ratesClient->request('GET', '');

        try {
            $responseContent = $response->getContent();
        } catch (ExceptionInterface $exception) {
            throw new RuntimeException('Rates API request failed. Error: ' . $exception->getMessage());
        }

        $ratesData = json_decode($responseContent, true);
        $rate = $ratesData['rates'][$transactionData->getCurrency()] ?? null;

        if (!$rate) {
            throw new RuntimeException('Currency was not found.');
        }

        return $rate;
    }
}
