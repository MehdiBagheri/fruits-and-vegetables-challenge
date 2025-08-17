<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Vegetable;

use App\Controller\Request\VegetableDto;
use App\Entity\Vegetable;
use App\Service\Vegetable\VegetablePersister;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;

final class VegetablePersisterTest extends IntegrationTestCase
{
    #[Test]
    public function shouldPersistVegetableWithGramUnit(): void
    {
        $vegetableDto = $this->createVegetableDto(name: 'Carrot', quantity: 500, unit: 'g');

        $vegetable = $this->sut()->persist($vegetableDto);

        $this->assertEquals('Carrot', $vegetable->getName());
        $this->assertEquals(500, $vegetable->getQuantity());

        $savedVegetable = $this->entityManager->getRepository(Vegetable::class)->find($vegetable->getId());
        $this->assertNotNull($savedVegetable);
        $this->assertEquals('Carrot', $savedVegetable->getName());
        $this->assertEquals(500, $savedVegetable->getQuantity());
    }

    #[Test]
    public function shouldPersistVegetableWithKilogramUnit(): void
    {
        $vegetableDto = $this->createVegetableDto(name: 'Potato', quantity: 2, unit: 'kg');

        $vegetable = $this->sut()->persist($vegetableDto);

        $this->assertEquals('Potato', $vegetable->getName());
        $this->assertEquals(2000, $vegetable->getQuantity());

        $savedVegetable = $this->entityManager->getRepository(Vegetable::class)->find($vegetable->getId());
        $this->assertEquals(2000, $savedVegetable->getQuantity());
    }

    #[Test]
    public function shouldFlushEntityToDatabase(): void
    {
        $vegetableDto = $this->createVegetableDto(name: 'Broccoli', quantity: 300, unit: 'g');

        $vegetable = $this->sut()->persist($vegetableDto);

        $this->entityManager->clear();

        $reloadedVegetable = $this->entityManager->getRepository(Vegetable::class)->find($vegetable->getId());
        $this->assertNotNull($reloadedVegetable);
        $this->assertEquals('Broccoli', $reloadedVegetable->getName());
        $this->assertEquals(300, $reloadedVegetable->getQuantity());
    }

    #[Test]
    public function shouldThrowValueErrorExceptionOnInValidUnit(): void
    {
        $vegetableDto = $this->createVegetableDto(name: 'Broccoli', quantity: 300, unit: 'pound');
        $this->expectException(\ValueError::class);
        $this->sut()->persist($vegetableDto);
    }

    #[Test]
    public function shouldThrowValueErrorExceptionOnNegativeQuantity(): void
    {
        $vegetableDto = $this->createVegetableDto(name: 'Broccoli', quantity: -300, unit: 'pound');
        $this->expectException(\ValueError::class);
        $this->sut()->persist($vegetableDto);
    }

    private function sut(): VegetablePersister
    {
        return self::getContainer()->get(VegetablePersister::class);
    }

    private function createVegetableDto(string $name, int $quantity, string $unit): VegetableDto
    {
        return new VegetableDto(
            name: $name,
            quantity: $quantity,
            unit: $unit
        );
    }
}
