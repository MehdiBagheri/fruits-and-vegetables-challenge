<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Request\FruitDto;
use App\Controller\Response\Serializer\ApiResponseSerializer;
use App\Entity\Fruit;
use App\Enum\Unit;
use App\Service\Fruit\FruitPersister;
use App\Service\Fruit\FruitSearcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class FruitController
{
    public function __construct(
        private readonly ApiResponseSerializer $serializer,
    ) {
    }

    #[Route(path: '/fruits', name: 'fruits_list', methods: ['GET'])]
    public function fruits(
        FruitSearcher $fruitsSearcher,
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $filter = '',
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] Unit $unit = Unit::GRAM,
    ): JsonResponse {
        $fruits = $fruitsSearcher->search($filter);

        return JsonResponse::fromJsonString($this->serializer->serializeCollection($fruits, $unit), Response::HTTP_OK);
    }

    #[Route(path: '/fruits', name: 'fruit_persist', methods: ['POST'])]
    public function persist(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] FruitDto $fruitDto,
        FruitPersister $fruitPersister,
    ): JsonResponse {
        $fruit = $fruitPersister->persist($fruitDto);

        return JsonResponse::fromJsonString($this->serializer->serialize($fruit), Response::HTTP_CREATED);
    }

    #[Route(path: '/fruits/{id}', name: 'fruit_show', methods: ['GET'])]
    public function retrieve(
        Fruit $fruit,
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] Unit $unit = Unit::GRAM,
    ): JsonResponse {
        return JsonResponse::fromJsonString($this->serializer->serialize($fruit, $unit), Response::HTTP_OK);
    }
}
