<?php

declare(strict_types=1);

namespace App\Service\RatesProvider;

use App\Dto\TransactionData;
use RuntimeException;

interface RatesProviderInterface
{

    /**
     * @throws RuntimeException Failed to retrieve data.
     */
    function getRate(TransactionData $transactionData): float;
}
