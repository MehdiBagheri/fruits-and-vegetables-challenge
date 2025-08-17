<?php

namespace App\DataFixtures;

use App\ProduceRequest\ProduceRequestProcessor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function __construct(private readonly ProduceRequestProcessor $produceRequestProcessor)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $content = (string) file_get_contents('request.json');
        $this->produceRequestProcessor->process($content);
    }
}
