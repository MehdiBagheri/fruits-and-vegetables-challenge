<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Response\Serializer\ApiResponseSerializer;
use App\Enum\Unit;
use App\Service\Vegetable\VegetableSearcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class VegetableController
{
    public function __construct(
        private readonly ApiResponseSerializer $serializer,
    ) {
    }

    #[Route(path: '/vegetables', name: 'vegetables_list', methods: ['GET'])]
    public function vegetables(
        VegetableSearcher $vegetableSearcher,
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $filter = '',
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] Unit $unit = Unit::GRAM,
    ): JsonResponse {
        $vegetables = $vegetableSearcher->search($filter);

        return JsonResponse::fromJsonString($this->serializer->serializeCollection($vegetables, $unit), Response::HTTP_OK);
    }
}
