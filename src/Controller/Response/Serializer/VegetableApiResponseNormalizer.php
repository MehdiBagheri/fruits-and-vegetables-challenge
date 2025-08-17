<?php

declare(strict_types=1);

namespace App\Controller\Response\Serializer;

use App\Entity\Vegetable;

final class VegetableApiResponseNormalizer extends ApiResponseNormalizer
{
    protected function generateLinks(object $entity): array
    {
        if (!$entity instanceof Vegetable) {
            throw new \InvalidArgumentException('Unsupported entity type');
        }

        return [
            'self' => "/vegetables/{$entity->getId()}",
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Vegetable;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Vegetable::class => true];
    }
}
