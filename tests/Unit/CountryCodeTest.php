<?php

declare(strict_types=1);

namespace Unit;

use App\Enum\CountryCode;
use PHPUnit\Framework\TestCase;

class CountryCodeTest extends TestCase
{
    public function testSuccess(): void
    {
        $this->assertTrue(CountryCode::isEu('DK'));
        $this->assertFalse(CountryCode::isEu('randomValue'));
    }
}