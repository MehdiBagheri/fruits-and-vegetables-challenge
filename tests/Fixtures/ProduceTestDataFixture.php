<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

final class ProduceTestDataFixture
{
    public static function validProduceItem(): array
    {
        return [
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 100,
            'unit' => 'kg',
            'type' => 'fruit',
        ];
    }

    public static function validVegetableItem(): array
    {
        return [
            'id' => 2,
            'name' => 'Carrot',
            'quantity' => 500,
            'unit' => 'g',
            'type' => 'vegetable',
        ];
    }

    public static function validProduceCollection(): array
    {
        return [
            self::validProduceItem(),
            self::validVegetableItem(),
        ];
    }

    public static function invalidProduceWithNegativeId(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'id' => -1, // Invalid: negative ID
            ],
        ];
    }

    public static function invalidProduceWithNumbersInName(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'name' => 'Apple123', // Invalid: contains numbers
            ],
        ];
    }

    public static function invalidProduceWithInvalidUnit(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'unit' => 'pounds', // Invalid: not kg or g
            ],
        ];
    }

    public static function invalidProduceWithInvalidType(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'type' => 'meat', // Invalid: not fruit or vegetable
            ],
        ];
    }

    public static function invalidProduceWithMissingFields(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Apple',
                // Missing quantity, unit, type
            ],
        ];
    }

    public static function invalidProduceWithExtraFields(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'extraField' => 'not allowed', // Extra field
            ],
        ];
    }

    public static function emptyCollection(): array
    {
        return [];
    }

    public static function invalidProduceWithZeroQuantity(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'quantity' => 0, // Invalid: not positive
            ],
        ];
    }

    public static function invalidProduceWithNegativeQuantity(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'quantity' => -1, // Invalid: not positive
            ],
        ];
    }

    public static function invalidProduceWithBlankName(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'name' => '', // Invalid: blank name
            ],
        ];
    }

    public static function invalidProduceWithSpecialCharsInName(): array
    {
        return [
            [
                ...self::validProduceItem(),
                'name' => 'Apple@#$', // Invalid: special characters
            ],
        ];
    }
}