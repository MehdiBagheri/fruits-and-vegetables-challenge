<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Trait\InitTestDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class IntegrationTestCase extends KernelTestCase
{
    use InitTestDatabaseTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->createDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
