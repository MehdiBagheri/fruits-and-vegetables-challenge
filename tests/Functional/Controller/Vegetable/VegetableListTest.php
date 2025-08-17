<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Vegetable;

use App\Tests\Functional\FunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class VegetableListTest extends FunctionalTestCase
{
    use CreateTestVegetableTrait;
    #[Test]
    public function shouldReturnEmptyArrayWhenNoFruits(): void
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('vegetables_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertEmpty($data['data']);
    }

    #[Test]
    public function shouldReturnFruitsWithBasicPropertiesAndLinks(): void
    {
        $this->createTestVegetable('Tomatoes', 2500);
        $this->createTestVegetable('Celery', 1200);
        $this->client->request(Request::METHOD_GET, $this->generateUrl('vegetables_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(2, $response['data']);

        $fruit = $response['data'][0];
        $this->assertSame('Tomatoes', $fruit['name']);
        $this->assertSame(2500.0, $fruit['quantity']);
        $this->assertArrayHasKey('links', $fruit);
        $this->assertArrayHasKey('self', $fruit['links']);
        $this->assertSame('/vegetables/1', $fruit['links']['self']);
        $this->assertEquals('g', $fruit['unit']);
    }

    #[Test]
    public function shouldReturnInGram(): void
    {
        $this->createTestVegetable('Tomatoes', 2500);
        $this->client->request(Request::METHOD_GET, $this->generateUrl('vegetables_list', ['unit' => 'g']));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $gramResponse = $this->client->getResponse();
        $gramData = json_decode($gramResponse->getContent(), true);
        $this->assertArrayHasKey('data', $gramData);
        $this->assertEquals(2500.0, $gramData['data'][0]['quantity']);
        $this->assertEquals('g', $gramData['data'][0]['unit']);
    }

    #[Test]
    public function shouldReturnInKilogram(): void
    {
        $this->createTestVegetable('Tomatoes', 2500);
        $this->client->request(Request::METHOD_GET, $this->generateUrl('vegetables_list', ['unit' => 'kg']));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $gramResponse = $this->client->getResponse();
        $kgData = json_decode($gramResponse->getContent(), true);
        $this->assertArrayHasKey('data', $kgData);
        $this->assertEquals(2.5, $kgData['data'][0]['quantity']);
        $this->assertEquals('kg', $kgData['data'][0]['unit']);
    }

    #[Test]
    public function shouldReturnBadRequest(): void
    {
        $this->createTestVegetable('Tomatoes', 2500);
        $this->client->request(Request::METHOD_GET, $this->generateUrl('vegetables_list', ['unit' => 'pound']));
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonStringEqualsJsonString(
            '{"errors":[{"status":400,"code":"BAD_REQUEST","message":"Invalid request."}]}',
            $this->client->getResponse()->getContent()
        );
    }

    #[Test]
    public function shouldFilterFruitsByName(): void
    {
        $this->createTestVegetable('Tomatoes', 1000);

        $this->client->request(Request::METHOD_GET, $this->generateUrl('vegetables_list', ['filter' => 'mat']));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('data', $response);
        $this->assertCount(1, $response['data']);
        $fruitNames = array_column($response['data'], 'name');
        $this->assertContains('Tomatoes', $fruitNames);
    }
}
