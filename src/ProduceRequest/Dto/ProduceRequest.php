<?php

declare(strict_types=1);

namespace App\ProduceRequest\Dto;

use App\Utility\ArrayCollection;

final readonly class ProduceRequest
{
    public function __construct(
        public ArrayCollection $fruits,
        public ArrayCollection $vegetables,
    ) {
    }

    public function all(): ArrayCollection
    {
        return new ArrayCollection([...$this->fruits, ...$this->vegetables]);
    }
}
