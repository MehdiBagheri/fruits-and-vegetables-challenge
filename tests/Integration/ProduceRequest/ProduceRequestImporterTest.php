<?php

declare(strict_types=1);

namespace App\Tests\Integration\ProduceRequest;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\ProduceRequest\ProduceRequestProcessor;
use App\Tests\Fixtures\ProduceTestDataFixture;
use App\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ProduceRequestProcessor::class)]
final class ProduceRequestImporterTest extends IntegrationTestCase
{
    #[Test]
    public function shouldImportProduceToInMemoryDatabase(): void
    {
        $jsonContent = json_encode(ProduceTestDataFixture::validProduceCollection());

        $this->sut()->process($jsonContent);

        $fruitRepo = $this->entityManager->getRepository(Fruit::class);
        $vegetableRepo = $this->entityManager->getRepository(Vegetable::class);

        $fruits = $fruitRepo->findAll();
        $vegetables = $vegetableRepo->findAll();

        $this->assertCount(1, $fruits);
        $this->assertCount(1, $vegetables);

        $this->assertEquals('Apple', $fruits[0]->getName());
        $this->assertEquals(100000, $fruits[0]->getQuantity());
        $this->assertEquals('Carrot', $vegetables[0]->getName());
        $this->assertEquals(500, $vegetables[0]->getQuantity());
    }

    #[Test]
    public function shouldRollbackTransactionOnValidationError(): void
    {
        $invalidJson = json_encode(ProduceTestDataFixture::invalidProduceWithNegativeId());

        $this->expectException(\InvalidArgumentException::class);

        try {
            $this->sut()->process($invalidJson);
        } finally {
            $fruitRepo = $this->entityManager->getRepository(Fruit::class);
            $vegetableRepo = $this->entityManager->getRepository(Vegetable::class);

            $this->assertCount(0, $fruitRepo->findAll());
            $this->assertCount(0, $vegetableRepo->findAll());
        }
    }

    #[Test]
    public function shouldImportMixedProduceTypes(): void
    {
        $mixedData = [
            [
                'id' => 1,
                'name' => 'Apple',
                'quantity' => 10,
                'unit' => 'kg',
                'type' => 'fruit',
            ],
            [
                'id' => 2,
                'name' => 'Banana',
                'quantity' => 2000,
                'unit' => 'g',
                'type' => 'fruit',
            ],
            [
                'id' => 3,
                'name' => 'Carrot',
                'quantity' => 5,
                'unit' => 'kg',
                'type' => 'vegetable',
            ],
            [
                'id' => 4,
                'name' => 'Broccoli',
                'quantity' => 800,
                'unit' => 'g',
                'type' => 'vegetable',
            ],
        ];

        $jsonContent = json_encode($mixedData);

        $this->sut()->process($jsonContent);

        $fruitRepo = $this->entityManager->getRepository(Fruit::class);
        $vegetableRepo = $this->entityManager->getRepository(Vegetable::class);

        $fruits = $fruitRepo->findAll();
        $vegetables = $vegetableRepo->findAll();

        $this->assertCount(2, $fruits);
        $this->assertCount(2, $vegetables);
    }

    #[Test]
    public function shouldHandleEmptyImport(): void
    {
        $emptyJson = json_encode(ProduceTestDataFixture::emptyCollection());

        $this->sut()->process($emptyJson);

        $fruitRepo = $this->entityManager->getRepository(Fruit::class);
        $vegetableRepo = $this->entityManager->getRepository(Vegetable::class);

        $this->assertCount(0, $fruitRepo->findAll());
        $this->assertCount(0, $vegetableRepo->findAll());
    }

    #[Test]
    public function shouldPersistEntitiesWithCorrectProperties(): void
    {
        $testData = [
            [
                'id' => 100,
                'name' => 'Test Fruit',
                'quantity' => 25,
                'unit' => 'kg',
                'type' => 'fruit',
            ],
        ];

        $jsonContent = json_encode($testData);

        $this->sut()->process($jsonContent);

        $fruitRepo = $this->entityManager->getRepository(Fruit::class);
        $fruit = $fruitRepo->findOneBy(['name' => 'Test Fruit']);

        $this->assertInstanceOf(Fruit::class, $fruit);
        $this->assertEquals('Test Fruit', $fruit->getName());
        $this->assertEquals(25000, $fruit->getQuantity());

        $this->assertNotNull($fruit->getId());
    }

    private function sut(): ProduceRequestProcessor
    {
        return self::getContainer()->get(ProduceRequestProcessor::class);
    }
}
