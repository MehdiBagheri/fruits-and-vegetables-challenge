<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Fruit;

use App\Controller\Request\FruitDto;
use App\Entity\Fruit;
use App\Service\Fruit\FruitPersister;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;

final class FruitPersisterTest extends IntegrationTestCase
{
    #[Test]
    public function shouldPersistFruitWithGramUnit(): void
    {
        $fruitDto = $this->createFruitDto(name: 'Apple', quantity: 300, unit: 'g');

        $fruit = $this->sut()->persist($fruitDto);

        $this->assertEquals('Apple', $fruit->getName());
        $this->assertEquals(300, $fruit->getQuantity());

        $savedFruit = $this->entityManager->getRepository(Fruit::class)->find($fruit->getId());
        $this->assertNotNull($savedFruit);
        $this->assertEquals('Apple', $savedFruit->getName());
        $this->assertEquals(300, $savedFruit->getQuantity());
    }

    #[Test]
    public function shouldPersistFruitWithKilogramUnit(): void
    {
        $fruitDto = $this->createFruitDto(name: 'Watermelon', quantity: 3, unit: 'kg');

        $fruit = $this->sut()->persist($fruitDto);

        $this->assertEquals('Watermelon', $fruit->getName());
        $this->assertEquals(3000, $fruit->getQuantity());

        $savedFruit = $this->entityManager->getRepository(Fruit::class)->find($fruit->getId());
        $this->assertEquals(3000, $savedFruit->getQuantity());
    }

    #[Test]
    public function shouldFlushEntityToDatabase(): void
    {
        $fruitDto = $this->createFruitDto(name: 'Mango', quantity: 250, unit: 'g');

        $fruit = $this->sut()->persist($fruitDto);

        $this->entityManager->clear();

        $reloadedFruit = $this->entityManager->getRepository(Fruit::class)->find($fruit->getId());
        $this->assertNotNull($reloadedFruit);
        $this->assertEquals('Mango', $reloadedFruit->getName());
        $this->assertEquals(250, $reloadedFruit->getQuantity());
    }

    #[Test]
    public function shouldThrowValueErrorExceptionInvalidUnit(): void
    {
        $fruitDto = $this->createFruitDto(name: 'Mango', quantity: 250, unit: 'pound');
        $this->expectException(\ValueError::class);
        $this->sut()->persist($fruitDto);
    }

    #[Test]
    public function shouldThrowValueErrorExceptionOnNegativeQuantity(): void
    {
        $fruitDto = $this->createFruitDto(name: 'Mango', quantity: -100, unit: 'pound');
        $this->expectException(\ValueError::class);
        $this->sut()->persist($fruitDto);
    }

    private function sut(): FruitPersister
    {
        return self::getContainer()->get(FruitPersister::class);
    }

    private function createFruitDto(string $name, int $quantity, string $unit): FruitDto
    {
        return new FruitDto(
            name: $name,
            quantity: $quantity,
            unit: $unit
        );
    }
}
