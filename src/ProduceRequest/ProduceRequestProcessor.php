<?php

declare(strict_types=1);

namespace App\ProduceRequest;

use Doctrine\ORM\EntityManagerInterface;

final readonly class ProduceRequestProcessor
{
    public function __construct(
        private ProduceRequestParser $produceRequestParser,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function process(string $content): void
    {
        $produceRequest = $this->produceRequestParser->parse($content);
        $this->entityManager->wrapInTransaction(function () use ($produceRequest) {
            foreach ($produceRequest->all() as $produce) {
                $this->entityManager->persist($produce);
            }
            $this->entityManager->flush();
        });
    }
}
