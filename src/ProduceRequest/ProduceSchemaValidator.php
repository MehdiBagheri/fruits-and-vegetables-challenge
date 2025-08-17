<?php

declare(strict_types=1);

namespace App\ProduceRequest;

use App\Enum\ProduceType;
use App\Enum\Unit;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProduceSchemaValidator
{
    private const ITEM_SCHEMA = [
        'id' => [Type::class => 'integer', NotNull::class => null, Positive::class => null],
        'name' => [
            Type::class => 'string',
            NotBlank::class => null,
            Length::class => ['min' => 1, 'max' => 100],
            Regex::class => ['pattern' => '/^[a-zA-Z\s]+$/'], // prevent special chars in name
        ],
        'quantity' => [Type::class => 'integer', NotNull::class => null, Positive::class => null],
        'unit' => [
            Type::class => 'string',
            NotBlank::class => null,
            Choice::class => [Unit::KG->value, Unit::GRAM->value],
        ],
        'type' => [
            Type::class => 'string',
            NotBlank::class => null,
            Choice::class => [ProduceType::Vegetable->value, ProduceType::Fruit->value],
        ],
    ];

    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate(array $data): void
    {
        $constraints = new All([
            new Type(type: 'array'),
            new Collection(
                fields: $this->buildConstraintsForItem(),
                allowExtraFields: false,
                allowMissingFields: false,
            ),
        ]);
        $violations = $this->validator->validate($data, $constraints);
        if (count($violations) > 0) {
            throw new \InvalidArgumentException('Provided JSON does not have proper schema format!');
        }
    }

    private function buildConstraintsForItem(): array
    {
        $constraints = [];
        foreach (self::ITEM_SCHEMA as $fieldName => $fieldConstraints) {
            foreach ($fieldConstraints as $constraintClass => $constraintConfig) {
                $constraints[$fieldName][] = match ($constraintClass) {
                    Type::class => new Type($constraintConfig),
                    NotNull::class => new NotNull(),
                    NotBlank::class => new NotBlank(),
                    Positive::class => new Positive(),
                    Length::class => new Length(min: $constraintConfig['min'], max: $constraintConfig['max']),
                    Regex::class => new Regex(pattern: $constraintConfig['pattern']),
                    Choice::class => new Choice(choices: $constraintConfig),
                    default => throw new \InvalidArgumentException("Unsupported constraint: {$constraintClass}"),
                };
            }
        }

        return $constraints;
    }
}
