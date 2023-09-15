<?php

declare(strict_types=1);

namespace App\Service\BinProvider;

interface BinProviderInterface
{
    function getCountryCodeByBin(string $bin): string;
}