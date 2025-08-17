<?php

declare(strict_types=1);

namespace App\Tests\Unit\ProduceRequest;

use App\ProduceRequest\ProduceSchemaValidator;
use App\Tests\Fixtures\ProduceTestDataFixture;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

#[CoversClass(ProduceSchemaValidator::class)]
final class ProduceSchemaValidatorTest extends TestCase
{
    #[Test]
    public function shouldPassValidationWithValidData(): void
    {
        $validData = ProduceTestDataFixture::validProduceCollection();

        $this->sut()->validate($validData);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function shouldFailWithInvalidId(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithNegativeId();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided JSON does not have proper schema format!');

        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldFailWithInvalidName(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithNumbersInName();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldFailWithInvalidUnit(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithInvalidUnit();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldFailWithInvalidType(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithInvalidType();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldFailWithMissingFields(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithMissingFields();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldFailWithExtraFields(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithExtraFields();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldPassWithEmptyArray(): void
    {
        $emptyData = ProduceTestDataFixture::emptyCollection();

        $this->sut()->validate($emptyData);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function shouldFailWithZeroQuantity(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithZeroQuantity();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldFailWithNegativeQuantity(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithNegativeQuantity();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldFailWithBlankName(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithBlankName();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    #[Test]
    public function shouldFailWithSpecialCharsInName(): void
    {
        $invalidData = ProduceTestDataFixture::invalidProduceWithSpecialCharsInName();

        $this->expectException(\InvalidArgumentException::class);
        $this->sut()->validate($invalidData);
    }

    private function sut(): ProduceSchemaValidator
    {
        $validator = Validation::createValidator();
        return new ProduceSchemaValidator($validator);
    }
}
