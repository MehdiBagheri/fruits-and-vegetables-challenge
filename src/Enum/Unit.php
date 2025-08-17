<?php

namespace App\Enum;

enum Unit: string
{
    case KG = 'kg';
    case GRAM = 'g';

    public function toGram(int $quantity): int
    {
        return match ($this) {
            self::KG => (int) (1000 * $quantity),
            self::GRAM => $quantity,
        };
    }

    public function toKilogram(int $quantity): float
    {
        return match ($this) {
            self::KG => $quantity,
            self::GRAM => $quantity / 1000,
        };
    }
}
