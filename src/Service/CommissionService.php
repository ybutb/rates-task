<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TransactionData;
use App\Enum\CountryCode;
use App\Service\BinProvider\BinProviderInterface;
use App\Service\RatesProvider\RatesProviderInterface;
use App\Storage\TransactionStorageInterface;

class CommissionService
{
    public const CURRENCY_EUR = 'EUR';

    public function __construct(
        private readonly TransactionStorageInterface $transactionStorage,
        private readonly BinProviderInterface $binProvider,
        private readonly RatesProviderInterface $ratesProvider
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
        $cardCountryCode = $this->binProvider->getCountryCodeByBin($accountData->getBin());
        $currentRate = $this->ratesProvider->getRate($accountData);
        $amountFixed = $accountData->getAmount();

        if ($accountData->getCurrency() !== self::CURRENCY_EUR) {
            $amountFixed = $currentRate ? $amountFixed / $currentRate : $amountFixed;
        }

        $commissionRate = CountryCode::isEu($cardCountryCode) ? 0.01 : 0.02;

        return $this->roundCommission($amountFixed * $commissionRate);
    }

    private function roundCommission(float $commission): float
    {
        return round(ceil($commission * 100) / 100, 2);
    }
}
