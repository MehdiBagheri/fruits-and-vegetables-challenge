<?php

declare(strict_types=1);

namespace App\Controller\Response\Serializer;

use App\Enum\Unit;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiResponseSerializer
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function serialize(object $entity, Unit $unit = Unit::GRAM): string
    {
        return $this->serializer->serialize($entity, 'json', ['groups' => 'api', 'unit' => $unit]);
    }

    public function serializeCollection(array $entities, Unit $unit = Unit::GRAM): string
    {
        return $this->serializer->serialize(['data' => $entities], 'json', ['groups' => 'api', 'unit' => $unit]);
    }
}
