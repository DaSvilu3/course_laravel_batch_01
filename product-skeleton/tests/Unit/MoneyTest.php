<?php

namespace Tests\Unit;

use App\Support\Money;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    public function test_it_converts_rials_to_baisa(): void
    {
        $this->assertSame(12_500, Money::toBaisa('12.500'));
        $this->assertSame(12_500, Money::toBaisa(12.5));
        $this->assertSame(1_000, Money::toBaisa(1));
        $this->assertSame(0, Money::toBaisa(0));
    }

    public function test_it_converts_baisa_back_to_rials(): void
    {
        $this->assertSame(12.5, Money::toDecimal(12_500));
        $this->assertSame('12.500', Money::decimalString(12_500));
        $this->assertSame('0.100', Money::decimalString(100));
    }

    public function test_conversion_survives_the_float_rounding_trap(): void
    {
        // 0.1 + 0.2 famously !== 0.3 in floating point; in baisa it is exact.
        $this->assertSame(300, Money::toBaisa('0.1') + Money::toBaisa('0.2'));
    }

    public function test_it_formats_per_locale(): void
    {
        app()->setLocale('en');
        $this->assertSame('OMR 12.500', Money::format(12_500));

        app()->setLocale('ar');
        $this->assertSame('12.500 ر.ع.', Money::format(12_500));
    }
}
