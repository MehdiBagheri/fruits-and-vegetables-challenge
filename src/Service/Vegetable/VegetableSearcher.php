<?php

declare(strict_types=1);

namespace App\Service\Vegetable;

use App\Repository\VegetableRepository;

final class VegetableSearcher
{
    public function __construct(private readonly VegetableRepository $vegetableRepository)
    {
    }

    public function search(string $searchTerm = ''): array
    {
        return '' === $searchTerm ? $this->vegetableRepository->findAll() : $this->vegetableRepository->findByName($searchTerm);
    }
}
