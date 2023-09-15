<?php

declare(strict_types=1);

namespace Unit;

use App\Dto\TransactionData;
use App\Service\BinProvider\DefaultBinProvider;
use App\Service\RatesProvider\DefaultRatesProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

class DefaultRatesProviderTest extends TestCase
{
    public function testSuccess(): void
    {
        $expectedRate = 7.459788;

        $responseContentMock = [
            "success" => true,
            "timestamp" => 1694695263,
            "base" => "EUR",
            "date" => "2023-09-14",
            "rates" => [
                "CRC" => 568.763003,
                "CUC" => 1.06694,
                "CUP" => 28.273909,
                "CVE" => 109.580465,
                "CZK" => 24.460674,
                "DJF" => 189.955155,
                "DKK" => $expectedRate,
                "DOP" => 60.563371,
                "DZD" => 146.755438,
                "EGP" => 32.968554,
                "ERN" => 16.004099,
            ]
        ];

        $mockResponse = new JsonMockResponse($responseContentMock);
        $mockClient = new MockHttpClient($mockResponse);
        $binProvider = new DefaultRatesProvider($mockClient);

        $transactionData = new TransactionData(
            '123456',
            'DKK',
            '1000.00'
        );

        $result = $binProvider->getRate($transactionData);

        $this->assertEquals($expectedRate, $result);
    }

    public function testNoRateResponseError(): void
    {
        $responseContentMock = [
            "success" => true,
            "timestamp" => 1694695263,
            "base" => "EUR",
            "date" => "2023-09-14",
            "rates" => [
                "CRC" => 568.763003,
                "CUC" => 1.06694,
                "CUP" => 28.273909,
                "CVE" => 109.580465,
                "CZK" => 24.460674,
                "DJF" => 189.955155,
                "DOP" => 60.563371,
                "DZD" => 146.755438,
                "EGP" => 32.968554,
                "ERN" => 16.004099,
            ]
        ];

        $mockResponse = new JsonMockResponse($responseContentMock);
        $mockClient = new MockHttpClient($mockResponse);
        $binProvider = new DefaultRatesProvider($mockClient);

        $transactionData = new TransactionData(
            '123456',
            'DKK',
            '1000.00'
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Currency was not found.');

        $binProvider->getRate($transactionData);
    }

    public function testRatesApiError(): void
    {
        $mockResponse = new JsonMockResponse(
            [],
            [
                'response_headers' => ['content-type' => 'application/json'],
                'http_code' => 400
            ]
        );
        $mockClient = new MockHttpClient($mockResponse);
        $binProvider = new DefaultRatesProvider($mockClient);

        $transactionData = new TransactionData(
            '123456',
            'DKK',
            '1000.00'
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rates API request failed. Error: HTTP 400 returned for');

        $binProvider->getRate($transactionData);
    }
}