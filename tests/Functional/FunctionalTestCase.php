<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Trait\InitTestDatabaseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

abstract class FunctionalTestCase extends WebTestCase
{
    use InitTestDatabaseTrait;

    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;
    protected RouterInterface $router;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->router = self::getContainer()->get('router');
        $this->createDatabase();
    }

    protected function generateUrl(string $routeName, array $parameters = []): string
    {
        return $this->router->generate($routeName, $parameters);
    }
}
