<?php

declare(strict_types=1);

namespace App\Tests\Integration\ProduceRequest;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\ProduceRequest\ProduceRequestParser;
use App\Tests\Fixtures\ProduceTestDataFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(ProduceRequestParser::class)]
final class ProduceRequestParserTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    #[Test]
    public function shouldParseValidJsonIntoProduceRequestWithRealValidation(): void
    {
        $jsonContent = json_encode(ProduceTestDataFixture::validProduceCollection());

        $result = $this->sut()->parse($jsonContent);

        $this->assertCount(1, $result->fruits);
        $this->assertCount(1, $result->vegetables);
        $this->assertCount(2, $result->all());

        $fruit = $result->fruits->getIterator()->current();
        $this->assertInstanceOf(Fruit::class, $fruit);
        $this->assertEquals('Apple', $fruit->getName());
        $this->assertEquals(100000, $fruit->getQuantity());

        $vegetable = $result->vegetables->getIterator()->current();
        $this->assertInstanceOf(Vegetable::class, $vegetable);
        $this->assertEquals('Carrot', $vegetable->getName());
        $this->assertEquals(500, $vegetable->getQuantity());
    }

    #[Test]
    public function shouldThrowExceptionForInvalidJsonWithRealValidation(): void
    {
        $invalidJson = json_encode(ProduceTestDataFixture::invalidProduceWithNegativeId());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided JSON does not have proper schema format!');

        $this->sut()->parse($invalidJson);
    }

    #[Test]
    public function shouldThrowExceptionForMalformedJson(): void
    {
        $malformedJson = '{"invalid": json syntax}';

        $this->expectException(\JsonException::class);

        $this->sut()->parse($malformedJson);
    }

    #[Test]
    public function shouldThrowExceptionForNonArrayJson(): void
    {
        $nonArrayJson = json_encode('string instead of array');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided JSON is not valid!');

        $this->sut()->parse($nonArrayJson);
    }

    #[Test]
    public function shouldParseEmptyArraySuccessfully(): void
    {
        $emptyJson = json_encode(ProduceTestDataFixture::emptyCollection());

        $result = $this->sut()->parse($emptyJson);

        $this->assertCount(0, $result->fruits);
        $this->assertCount(0, $result->vegetables);
        $this->assertCount(0, $result->all());
    }

    #[Test]
    public function shouldSeparateFruitsAndVegetablesCorrectly(): void
    {
        // Create mixed data with multiple fruits and vegetables
        $mixedData = [
            ProduceTestDataFixture::validProduceItem(),
            ProduceTestDataFixture::validVegetableItem(),
            [
                'id' => 3,
                'name' => 'Banana',
                'quantity' => 50,
                'unit' => 'kg',
                'type' => 'fruit',
            ],
            [
                'id' => 4,
                'name' => 'Broccoli',
                'quantity' => 2000,
                'unit' => 'g',
                'type' => 'vegetable',
            ],
        ];

        $jsonContent = json_encode($mixedData);

        $result = $this->sut()->parse($jsonContent);

        $this->assertCount(2, $result->fruits);
        $this->assertCount(2, $result->vegetables);
        $this->assertCount(4, $result->all());

        // Verify all fruits are actually Fruit entities
        foreach ($result->fruits as $fruit) {
            $this->assertInstanceOf(Fruit::class, $fruit);
        }

        // Verify all vegetables are actually Vegetable entities
        foreach ($result->vegetables as $vegetable) {
            $this->assertInstanceOf(Vegetable::class, $vegetable);
        }
    }

    #[Test]
    public function shouldHandleUnitConversionCorrectly(): void
    {
        $dataWithDifferentUnits = [
            [
                'id' => 1,
                'name' => 'Apple',
                'quantity' => 5,
                'unit' => 'kg',
                'type' => 'fruit',
            ],
            [
                'id' => 2,
                'name' => 'Carrot',
                'quantity' => 1500,
                'unit' => 'g',
                'type' => 'vegetable',
            ],
        ];

        $jsonContent = json_encode($dataWithDifferentUnits);

        $result = $this->sut()->parse($jsonContent);

        $fruit = $result->fruits->getIterator()->current();
        $vegetable = $result->vegetables->getIterator()->current();

        $this->assertEquals(5000, $fruit->getQuantity());
        $this->assertEquals(1500, $vegetable->getQuantity());
    }

    private function sut(): ProduceRequestParser
    {
        return self::getContainer()->get(ProduceRequestParser::class);
    }
}
