<?php

declare(strict_types=1);

namespace App\Tests\Unit\Enum;

use App\Enum\Unit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Unit::class)]
final class UnitTest extends TestCase
{
    #[Test]
    public function shouldConvertToGram(): void
    {
        $gram = Unit::KG->toGram(1);
        $this->assertSame(1000, $gram);
    }

    #[Test]
    public function shouldConvertToKilogram(): void
    {
        $kilogram = Unit::GRAM->toKilogram(1);
        $this->assertSame(0.001, $kilogram);
    }
}
