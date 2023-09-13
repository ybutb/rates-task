<?php

declare(strict_types=1);

namespace App\Service\RatesProvider;

use App\Dto\TransactionData;

interface RatesProviderInterface
{
    function getData(TransactionData $accountData): float;
}
