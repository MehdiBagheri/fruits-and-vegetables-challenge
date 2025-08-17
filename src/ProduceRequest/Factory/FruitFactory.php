<?php

declare(strict_types=1);

namespace App\ProduceRequest\Factory;

use App\Entity\Fruit;

/**
 * @template TKey of array-key
 * @template TValue
 * @extends ProduceFactory<Fruit>
 */

final class FruitFactory extends ProduceFactory
{
    /** @param array<TKey, string|int> $data */
    public function create(array $data): object
    {
        $fruit = new Fruit();
        $fruit->setName((string) $data['name']);
        $fruit->setQuantity($this->getQuantityInGram((int) $data['quantity'], (string) $data['unit']));

        return $fruit;
    }
}
