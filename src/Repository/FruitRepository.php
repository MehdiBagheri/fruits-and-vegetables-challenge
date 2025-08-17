<?php

namespace App\Repository;

use App\Entity\Fruit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fruit>
 */
final class FruitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fruit::class);
    }

    /**
     * Find fruits by partial name match (case-insensitive).
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('fruit')
            ->where('LOWER(fruit.name) LIKE CONCAT(\'%\', LOWER(:name), \'%\')')
            ->setParameter('name', $name)
            ->orderBy('fruit.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
