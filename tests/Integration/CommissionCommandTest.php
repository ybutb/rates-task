<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Command\CommissionsCommand;
use App\Dto\TransactionData;
use App\Storage\TransactionStorage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CommissionCommandTest extends KernelTestCase
{
    public function setUp(): void
    {
        self::bootKernel();

        $this->container = static::getContainer();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSuccessEu($currency, $rate, $cardCountryCode)
    {
        $argv = [
            'app.php',
            'input.txt'
        ];

        $binResponsePayload = [
            'country' => [
                'alpha2' => $cardCountryCode
            ]
        ];

        $ratesResponsePayload = [
            'rates' => [
                'EUR' => $rate
            ]
        ];

        $transactionData = new TransactionData('45717360', $currency, '100.00');

        $this->container->set('bin_lookup_http_client', new MockHttpClient(new MockResponse(json_encode($binResponsePayload))));
        $this->container->set('rate_http_client', new MockHttpClient(new MockResponse(json_encode($ratesResponsePayload))));

        $storageMock = $this->createPartialMock(TransactionStorage::class, ['getDataByDsn']);
        $storageMock->expects($this->once())
            ->method('getDataByDsn')
            ->with('input.txt')
            ->willReturn([$transactionData]);

        $this->container->set(TransactionStorage::class, $storageMock);

        $this->container->get(CommissionsCommand::class)->run($argv);
        $result = $this->getActualOutput();
        $this->assertEquals('0.02', $result);
    }

    public function dataProvider(): array
    {
        return [
            [
                'currency' => 'EUR',
                'rate' => 2.0,
                'cardCountryCode' => '0.01'
            ]
        ];
    }

    public function testBinClientError(): void
    {

    }

    public function testRatesClientError(): void
    {

    }

    public function testInvalidInput(): void
    {

    }
}