<?php

namespace App\Tests\Functional\Controller\Fruit;

use App\Entity\Fruit;

trait CreateTestFruitTrait
{
    private function createTestFruit(string $name, int $quantityInGrams): Fruit
    {
        $fruit = new Fruit();
        $fruit->setName($name);
        $fruit->setQuantity($quantityInGrams);

        $this->entityManager->persist($fruit);
        $this->entityManager->flush();

        return $fruit;
    }
}
