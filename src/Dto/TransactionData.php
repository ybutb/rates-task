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

    /**
     * @return string
     */
    public function getBin(): string
    {
        return $this->bin;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }
}