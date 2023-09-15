<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Service\BinProvider\DefaultBinDataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

class DefaultBinDataProviderTest extends TestCase
{
    public function testSuccess(): void
    {
        $countryCode = 'DK';

        $responseContentMock = [
            "number" => [
                "length" => 16,
                "luhn" => true
            ],
            "scheme" => "visa",
            "type" => "debit",
            "brand" => "Visa/Dankort",
            "prepaid" => false,
            "country" => [
                "numeric" => "208",
                "alpha2" => $countryCode,
                "name" => "Denmark",
                "emoji" => "🇩🇰",
                "currency" => "DKK",
                "latitude" => 56,
                "longitude" => 10
            ],
            "bank" => [
                "name" => "Jyske Bank",
                "url" => "www.jyskebank.dk",
                "phone" => "+4589893300",
                "city" => "Hjørring"
            ]
        ];

        $mockResponse = new JsonMockResponse($responseContentMock);
        $mockClient = new MockHttpClient($mockResponse);
        $binProvider = new DefaultBinDataProvider($mockClient);

        $result = $binProvider->getCountryCodeByBin('123456');

        $this->assertEquals($countryCode, $result);
    }

    public function testWrongBinError(): void
    {
        $responseContentMock = [
            "number" => [
                "length" => 16,
                "luhn" => true
            ],
            "scheme" => "visa",
            "type" => "debit",
            "brand" => "Visa/Dankort",
            "prepaid" => false,
            "country" => [
                "numeric" => "208",
                "name" => "Denmark",
                "emoji" => "🇩🇰",
                "currency" => "DKK",
                "latitude" => 56,
                "longitude" => 10
            ],
            "bank" => [
                "name" => "Jyske Bank",
                "url" => "www.jyskebank.dk",
                "phone" => "+4589893300",
                "city" => "Hjørring"
            ]
        ];

        $mockResponse = new JsonMockResponse($responseContentMock);
        $mockClient = new MockHttpClient($mockResponse);
        $binProvider = new DefaultBinDataProvider($mockClient);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Bin is not valid.');

        $binProvider->getCountryCodeByBin('notValidBin');
    }

    public function testNoCountryCodeResponseError(): void
    {
        $mockClient = new MockHttpClient(new JsonMockResponse());
        $binProvider = new DefaultBinDataProvider($mockClient);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No card issuer country data.');

        $binProvider->getCountryCodeByBin('123456');
    }

    public function testBinApiError(): void
    {
        $mockResponse = new JsonMockResponse(
            [],
            [
                'response_headers' => ['content-type' => 'application/json'],
                'http_code' => 400
            ]
        );
        $mockClient = new MockHttpClient($mockResponse);
        $binProvider = new DefaultBinDataProvider($mockClient);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Bin provider API request failed. Error: HTTP 400 returned for');

        $binProvider->getCountryCodeByBin('123456');
    }
}