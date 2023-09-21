<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TransactionData;
use App\Enum\CountryCode;
use App\Service\BinProvider\BinDataProviderInterface;
use App\Service\RatesProvider\RatesProviderInterface;
use App\Storage\TransactionStorageInterface;
use Brick\Math\RoundingMode;
use Brick\Money\Context\DefaultContext;
use Brick\Money\RationalMoney;

class CommissionService
{
    private const CURRENCY_EUR = 'EUR';

    public function __construct(
        private readonly TransactionStorageInterface $transactionStorage,
        private readonly BinDataProviderInterface $binProvider,
        private readonly RatesProviderInterface $ratesProvider
    ) {
    }

    /**
     * @return float[]
     */
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
        $currencyRate = $this->ratesProvider->getRate($accountData);

        $commission = RationalMoney::of($accountData->getAmount(), self::CURRENCY_EUR);

        if ($currencyRate && $accountData->getCurrency() !== self::CURRENCY_EUR) {
            $commission = $commission->dividedBy($currencyRate);
        }

        $commissionRate = CountryCode::isEu($cardCountryCode) ? 0.01 : 0.02;
        $commission = $commission->multipliedBy($commissionRate);

        return $this->formatToCents($commission);
    }

    private function formatToCents(RationalMoney $commission): float
    {
        return $commission->to(new DefaultContext(), RoundingMode::CEILING)
            ->getAmount()
            ->toScale(2)
            ->toFloat();
    }
}
