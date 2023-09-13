<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TransactionData;
use App\Enum\CountryCode;
use App\Service\BinProvider\BinProviderInterface;
use App\Service\RatesProvider\RatesProviderInterface;
use App\Storage\TransactionStorage;

class CommissionService
{
    private const CURRENCY_EUR = 'EUR';

    public function __construct(
        private readonly TransactionStorage $transactionStorage,
        private readonly BinProviderInterface $binProviderClient,
        private readonly RatesProviderInterface $ratesProviderClient
    )
    {
    }

    public function getTransactionCommissions(string $dsn): array
    {
        $accountsData = $this->transactionStorage->getDataByDsn($dsn);
        $result = [];

        foreach ($accountsData as $accountData) {
            $result[] = $this->getCommission($accountData);
        }

        return $result;
    }

    private function getCommission(TransactionData $accountData): float
    {
        $cardCountryCode = $this->binProviderClient->getData($accountData->getBin());
        $currentRate = $this->ratesProviderClient->getData($accountData);
        $amountFixed = $accountData->getAmount();

        if ($accountData->getCurrency() !== self::CURRENCY_EUR || $currentRate > 0) {
            $amountFixed = $amountFixed / $currentRate;
        }

        $commissionRate = CountryCode::isEu($cardCountryCode) ? 0.01 : 0.02;

        return ceil($amountFixed * $commissionRate);
    }
}
