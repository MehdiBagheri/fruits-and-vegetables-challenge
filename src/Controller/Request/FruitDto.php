<?php

declare(strict_types=1);

namespace App\Controller\Request;

use App\Enum\Unit;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;

final readonly class FruitDto
{
    public function __construct(
        #[NotBlank]
        #[Length(min: 1, max: 100)]
        #[Regex(pattern: '/^[a-zA-Z\s]+$/')]
        public string $name,
        #[NotNull]
        #[Positive]
        public int $quantity,
        #[NotBlank]
        #[Choice(choices: [Unit::KG->value, Unit::GRAM->value])]
        public string $unit,
    ) {
    }
}
