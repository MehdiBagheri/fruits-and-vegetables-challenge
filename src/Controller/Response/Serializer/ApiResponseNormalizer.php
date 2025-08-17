<?php

declare(strict_types=1);

namespace App\Controller\Response\Serializer;

use App\Enum\Unit;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class ApiResponseNormalizer implements NormalizerInterface
{
    abstract protected function generateLinks(object $entity): array;
    abstract public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool;
    abstract public function getSupportedTypes(?string $format): array;

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $unit = $context['unit'] ?? Unit::GRAM;

        return [
            'id' => $data->getId(),
            'name' => $data->getName(),
            'quantity' => $this->convertQuantity($data->getQuantity(), $unit),
            'unit' => $unit->value,
            'links' => $this->generateLinks($data),
        ];
    }

    private function convertQuantity(int $quantityInGrams, Unit $unit): float
    {
        return match ($unit) {
            Unit::GRAM => (float) $quantityInGrams,
            Unit::KG => Unit::GRAM->toKilogram($quantityInGrams),
        };
    }
}
