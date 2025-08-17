<?php

namespace App\Tests\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

trait InitTestDatabaseTrait
{
    protected EntityManagerInterface $entityManager;
    private function createDatabase(): void
    {
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->updateSchema($metadata);
    }
}
