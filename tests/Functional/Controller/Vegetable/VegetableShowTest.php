<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Vegetable;

use App\Tests\Functional\FunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class VegetableShowTest extends FunctionalTestCase
{
    use CreateTestVegetableTrait;

    #[Test]
    public function shouldRetrieveSingleFruit(): void
    {
        $fruit = $this->createTestVegetable('Beetroot', 400);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('vegetable_show', ['id' => $fruit->getId()])
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Beetroot', $response['name']);
        $this->assertEquals(400.0, $response['quantity']);
        $this->assertEquals('g', $response['unit']);
        $this->assertArrayHasKey('links', $response);
    }

    #[Test]
    public function shouldRetrieveSingleFruitWithKgUnit(): void
    {
        $fruit = $this->createTestVegetable('Beetroot', 5000);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('vegetable_show', ['id' => $fruit->getId(), 'unit' => 'kg'])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals('Beetroot', $data['name']);
        $this->assertEquals(5.0, $data['quantity']);
        $this->assertEquals('kg', $data['unit']);
    }

    #[Test]
    public function shouldRetrieveSingleFruitWithGramUnit(): void
    {
        $fruit = $this->createTestVegetable('Beetroot', 5000);

        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('vegetable_show', ['id' => $fruit->getId(), 'unit' => 'g'])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals('Beetroot', $data['name']);
        $this->assertEquals(5000, $data['quantity']);
        $this->assertEquals('g', $data['unit']);
    }

    #[Test]
    public function shouldReturn404ErrorWhenFruitNotFound(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('vegetable_show', ['id' => 9999]));

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('errors', $data);
        $this->assertCount(1, $data['errors']);
        $error = $data['errors'][0];
        $this->assertEquals(Response::HTTP_NOT_FOUND, $error['status']);
        $this->assertEquals('NOT_FOUND', $error['code']);
        $this->assertEquals('Item not found.', $error['message']);
    }
}
