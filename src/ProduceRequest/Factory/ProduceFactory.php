<?php

declare(strict_types=1);

namespace App\ProduceRequest\Factory;

use App\Enum\Unit;

abstract class ProduceFactory
{
    abstract public function create(array $data): object;

    protected function getQuantityInGram(int $quantity, string $unit): int
    {
        $unit = Unit::from($unit);
        return $unit->toGram($quantity);
    }
}
