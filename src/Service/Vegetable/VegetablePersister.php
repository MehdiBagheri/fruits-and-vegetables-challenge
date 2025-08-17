<?php

declare(strict_types=1);

namespace App\Service\Vegetable;

use App\Controller\Request\VegetableDto;
use App\Entity\Vegetable;
use App\Enum\Unit;
use Doctrine\ORM\EntityManagerInterface;

final class VegetablePersister
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function persist(VegetableDto $vegetableDto): Vegetable
    {
        $vegetable = new Vegetable();
        $vegetable->setName($vegetableDto->name);
        $vegetable->setQuantity(Unit::from($vegetableDto->unit)->toGram($vegetableDto->quantity));

        $this->entityManager->persist($vegetable);
        $this->entityManager->flush();
        return $vegetable;
    }
}
