<?php

declare(strict_types=1);

namespace App\Controller\Response\Serializer;

use App\Entity\Fruit;

final class FruitApiResponseNormalizer extends ApiResponseNormalizer
{
    protected function generateLinks(object $entity): array
    {
        if (!$entity instanceof Fruit) {
            throw new \InvalidArgumentException('Unsupported entity type');
        }

        return [
            'self' => "/fruits/{$entity->getId()}",
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Fruit;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Fruit::class => true];
    }
}
