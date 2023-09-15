<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Dto\TransactionData;
use App\Enum\CountryCode;
use App\Service\BinProvider\BinDataProviderInterface;
use App\Service\CommissionService;
use App\Service\RatesProvider\RatesProviderInterface;
use App\Storage\TransactionStorageInterface;
use PHPUnit\Framework\TestCase;

class CommissionServiceTest extends TestCase
{
    public function testEuCardSuccess(): void
    {
        $mockRate = 4.0;
        $mockAmount = 1000.00;
        $mockedCountryCode = 'DK';

        $transactionData = new TransactionData(
            bin: '45717360',
            currency: 'DKK',
            amount: (string) $mockAmount,
        );

        $commissionService = $this->getService($mockedCountryCode, $transactionData, $mockRate);
        $expectedCommission =  $mockAmount / $mockRate * 0.01;

        $result = $commissionService->getTransactionCommissions('input.txt');

        $this->assertEquals([$expectedCommission], $result);
    }

    public function testNonEuCardSuccess(): void
    {
        $mockRate = 4.0;
        $mockAmount = 1000.00;
        $mockedCountryCode = 'UA';

        $transactionData = new TransactionData(
            bin: '45717360',
            currency: 'UAH',
            amount: (string) $mockAmount,
        );

        $commissionService = $this->getService($mockedCountryCode, $transactionData, $mockRate);
        $expectedCommission =  $mockAmount / $mockRate * 0.02;

        $result = $commissionService->getTransactionCommissions('input.txt');

        $this->assertEquals([$expectedCommission], $result);
    }

    public function testZeroRateSuccess(): void
    {
        $mockRate = 0;
        $mockAmount = 1000.00;
        $mockedCountryCode = 'DK';

        $transactionData = new TransactionData(
            bin: '45717360',
            currency: 'DKK',
            amount: (string) $mockAmount,
        );

        $commissionService = $this->getService($mockedCountryCode, $transactionData, $mockRate);
        $expectedCommission = $mockAmount * 0.01;

        $result = $commissionService->getTransactionCommissions('input.txt');

        $this->assertEquals([$expectedCommission], $result);
    }

    public function testEuroCurrencySuccess(): void
    {
        $mockRate = 5.0;
        $mockAmount = 1000.00;
        $mockedCountryCode = 'DE';

        $transactionData = new TransactionData(
            bin: '45717360',
            currency: 'EUR',
            amount: (string) $mockAmount,
        );

        $commissionService = $this->getService($mockedCountryCode, $transactionData, $mockRate);
        $expectedCommission =  $mockAmount * 0.01;

        $result = $commissionService->getTransactionCommissions('input.txt');

        $this->assertEquals([$expectedCommission], $result);
    }

    public function testCommissionByCentsCeiling(): void
    {
        $mockRate = 21.654;
        $mockAmount = 1000.00;
        $mockedCountryCode = 'DK';

        $transactionData = new TransactionData(
            bin: '45717360',
            currency: 'DKK',
            amount: (string) $mockAmount,
        );

        $commissionService = $this->getService($mockedCountryCode, $transactionData, $mockRate);
        $expectedCommissionWithCeiling = 0.47; // instead of 0.4618...

        $result = $commissionService->getTransactionCommissions('input.txt');

        $this->assertEquals([$expectedCommissionWithCeiling], $result);
    }

    private function getService(string $countryCode, TransactionData $transactionData, float $mockRate): CommissionService
    {
        $binProviderMock = $this->createMock(BinDataProviderInterface::class);

        $binProviderMock->expects($this->once())
            ->method('getCountryCodeByBin')
            ->willReturn($countryCode);

        $ratesProviderMock = $this->createMock(RatesProviderInterface::class);
        $ratesProviderMock->expects($this->once())
            ->method('getRate')
            ->willReturn($mockRate);

        $storageMock = $this->createMock(TransactionStorageInterface::class);
        $storageMock->expects($this->once())
            ->method('getDataByDsn')
            ->willReturn([$transactionData]);

        return new CommissionService($storageMock, $binProviderMock, $ratesProviderMock);
    }
}