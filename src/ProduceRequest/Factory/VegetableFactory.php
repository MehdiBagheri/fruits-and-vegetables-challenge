<?php

declare(strict_types=1);

namespace App\ProduceRequest\Factory;

use App\Entity\Vegetable;

/**
 * @template TKey of array-key
 * @template TValue
 * @extends ProduceFactory<Vegetable>
 */
final class VegetableFactory extends ProduceFactory
{
    /** @param array<TKey, string|int> $data */
    public function create(array $data): object
    {
        $vegetable = new Vegetable();
        $vegetable->setName((string) $data['name']);
        $vegetable->setQuantity($this->getQuantityInGram((int) $data['quantity'], (string) $data['unit']));

        return $vegetable;
    }
}
