<?php

declare(strict_types=1);

namespace App\Service\Fruit;

use App\Controller\Request\FruitDto;
use App\Entity\Fruit;
use App\Enum\Unit;
use Doctrine\ORM\EntityManagerInterface;

final class FruitPersister
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function persist(FruitDto $fruitDto): Fruit
    {
        $fruit = new Fruit();
        $fruit->setName($fruitDto->name);
        $fruit->setQuantity(Unit::from($fruitDto->unit)->toGram($fruitDto->quantity));

        $this->entityManager->persist($fruit);
        $this->entityManager->flush();
        return $fruit;
    }
}
