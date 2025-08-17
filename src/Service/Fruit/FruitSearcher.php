<?php

declare(strict_types=1);

namespace App\Service\Fruit;

use App\Repository\FruitRepository;

final class FruitSearcher
{
    public function __construct(private readonly FruitRepository $fruitRepository)
    {
    }

    public function search(string $searchTerm = ''): array
    {
        return '' === $searchTerm ? $this->fruitRepository->findAll() : $this->fruitRepository->findByName($searchTerm);
    }
}
