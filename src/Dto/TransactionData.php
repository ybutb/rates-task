<?php

declare(strict_types=1);

namespace App\Dto;

class TransactionData
{
    public function __construct(
        private readonly string $bin,
        private readonly string $currency,
        private readonly string $amount,
    ) {
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }
}