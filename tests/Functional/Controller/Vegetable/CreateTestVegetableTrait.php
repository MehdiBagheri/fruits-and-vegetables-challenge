<?php

namespace App\Tests\Functional\Controller\Vegetable;

use App\Entity\Vegetable;

trait CreateTestVegetableTrait
{
    private function createTestVegetable(string $name, int $quantityInGrams): Vegetable
    {
        $fruit = new Vegetable();
        $fruit->setName($name);
        $fruit->setQuantity($quantityInGrams);

        $this->entityManager->persist($fruit);
        $this->entityManager->flush();

        return $fruit;
    }
}
