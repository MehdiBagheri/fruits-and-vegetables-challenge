<?php

declare(strict_types=1);

namespace App\ProduceRequest;

use App\Enum\ProduceType;
use App\ProduceRequest\Dto\ProduceRequest;
use App\ProduceRequest\Factory\FruitFactory;
use App\ProduceRequest\Factory\VegetableFactory;
use App\Utility\ArrayCollection;

/**
 * @template TKey of array-key
 * @template TValue
 */
final class ProduceRequestParser
{
    public function __construct(
        private readonly ProduceSchemaValidator $produceSchemaValidator,
        private readonly FruitFactory $fruitFactory,
        private readonly VegetableFactory $vegetableFactory,
    ) {
    }

    public function parse(string $content): ProduceRequest
    {
        $decodedJson = $this->decodeJson($content);
        $allElements = new ArrayCollection($decodedJson);

        $produce = $allElements->reduce(callback: $this->processCollection(...), initial: []);

        return new ProduceRequest(
            fruits: new ArrayCollection($produce->get('fruits') ?? []),
            vegetables: new ArrayCollection($produce->get('vegetables') ?? [])
        );
    }

    /**
     * @param list<ArrayCollection>   $produce
     * @param array<TKey, string|int> $element
     */
    private function processCollection(array $produce, array $element): array
    {
        match (ProduceType::from((string) $element['type'])) {
            ProduceType::Fruit => $produce['fruits'][] = $this->fruitFactory->create($element),
            ProduceType::Vegetable => $produce['vegetables'][] = $this->vegetableFactory->create($element),
        };

        return $produce;
    }

    private function decodeJson(string $content): array
    {
        $decodedJson = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decodedJson)) {
            throw new \InvalidArgumentException('Provided JSON is not valid!');
        }
        $this->produceSchemaValidator->validate($decodedJson);

        return $decodedJson;
    }
}
