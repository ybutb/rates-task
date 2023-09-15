<?php

declare(strict_types=1);

namespace App\Service\BinProvider;

use RuntimeException;

interface BinDataProviderInterface
{
    /**
     * @throws RuntimeException Failed to retrieve data.
     */
    function getCountryCodeByBin(string $bin): string;
}