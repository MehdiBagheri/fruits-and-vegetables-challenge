<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Vegetable;

use App\Entity\Vegetable;
use App\Service\Vegetable\VegetableSearcher;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;

final class VegetableSearcherTest extends IntegrationTestCase
{
    #[Test]
    public function shouldReturnEmptyArrayWhenNoVegetables(): void
    {
        $result = $this->sut()->search();

        $this->assertTrue([] === $result);
    }

    #[Test]
    public function shouldReturnAllVegetablesWhenEmptySearchTerm(): void
    {
        $this->createTestVegetable('Carrot', 1000);
        $this->createTestVegetable('Broccoli', 800);
        $this->createTestVegetable('Potato', 1500);

        $result = $this->sut()->search();

        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(Vegetable::class, $result);
    }

    #[Test]
    public function shouldFilterVegetablesByName(): void
    {
        $this->createTestVegetable('Carrot', 1000);
        $this->createTestVegetable('Broccoli', 800);
        $this->createTestVegetable('Sweet Carrot', 1500);

        $result = $this->sut()->search('carrot');

        $this->assertCount(2, $result);

        $vegetableNames = array_map(fn (Vegetable $v) => $v->getName(), $result);
        $this->assertContains('Carrot', $vegetableNames);
        $this->assertContains('Sweet Carrot', $vegetableNames);
        $this->assertNotContains('Broccoli', $vegetableNames);
    }

    #[Test]
    public function shouldReturnEmptyArrayWhenNoMatches(): void
    {
        $this->createTestVegetable('Carrot', 1000);
        $this->createTestVegetable('Broccoli', 800);

        $result = $this->sut()->search('tomato');

        $this->assertTrue([] === $result);
    }

    #[Test]
    public function shouldPerformCaseInsensitiveSearch(): void
    {
        $this->createTestVegetable('Carrot', 1000);
        $this->createTestVegetable('BROCCOLI', 800);

        $result = $this->sut()->search('CARROT');

        $this->assertCount(1, $result);
        $this->assertEquals('Carrot', $result[0]->getName());
    }

    private function createTestVegetable(string $name, int $quantityInGrams): void
    {
        $vegetable = new Vegetable();
        $vegetable->setName($name);
        $vegetable->setQuantity($quantityInGrams);

        $this->entityManager->persist($vegetable);
        $this->entityManager->flush();
    }

    private function sut(): VegetableSearcher
    {
        return self::getContainer()->get(VegetableSearcher::class);
    }
}
