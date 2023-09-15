<?php

declare(strict_types=1);

namespace Unit;

use App\Dto\TransactionData;
use PHPUnit\Framework\TestCase;

class TransactionDataTest extends TestCase
{
    public function testSuccess(): void
    {
        $bin = '123456';
        $amount = '1000.00';
        $currency = 'DKK';

        $transactionData = new TransactionData(
            $bin,
            $currency,
            $amount
        );

        $this->assertEquals($bin, $transactionData->getBin());
        $this->assertEquals($amount, $transactionData->getAmount());
        $this->assertEquals($currency, $transactionData->getCurrency());
    }
}