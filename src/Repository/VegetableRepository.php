<?php

namespace App\Repository;

use App\Entity\Vegetable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vegetable>
 */
final class VegetableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vegetable::class);
    }

    /**
     * Find fruits by partial name match (case-insensitive).
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('vegetable')
                    ->where('LOWER(vegetable.name) LIKE CONCAT(\'%\', LOWER(:name), \'%\')')
                    ->setParameter('name', $name)
                    ->orderBy('vegetable.name', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}
