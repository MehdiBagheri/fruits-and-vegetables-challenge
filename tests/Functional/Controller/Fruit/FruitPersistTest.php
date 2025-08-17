<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Fruit;

use App\Tests\Functional\FunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FruitPersistTest extends FunctionalTestCase
{
    #[Test]
    public function shouldCreateFruitSuccessfully(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('fruit_persist'),
            [
                'name' => 'Orange',
                'quantity' => 300,
                'unit' => 'g',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Orange', $data['name']);
        $this->assertEquals(300.0, $data['quantity']);
        $this->assertEquals('g', $data['unit']);
        $this->assertArrayHasKey('links', $data);
    }

    #[Test]
    public function shouldReturnBadRequestOnInvalidUnit(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('fruit_persist'),
            [
                'name' => 'Orange',
                'quantity' => 300,
                'unit' => 'pound',
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonStringEqualsJsonString(
            '{"errors":[{"status":400,"code":"BAD_REQUEST","message":"Invalid request."}]}',
            $this->client->getResponse()->getContent()
        );
    }

    #[Test]
    public function shouldReturnBadRequestOnInvalidName(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('fruit_persist'),
            [
                'name' => 'Orange@!',
                'quantity' => 300,
                'unit' => 'kg',
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonStringEqualsJsonString(
            '{"errors":[{"status":400,"code":"BAD_REQUEST","message":"Invalid request."}]}',
            $this->client->getResponse()->getContent()
        );
    }

    #[Test]
    public function shouldReturnBadRequestOnMissingField(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('fruit_persist'),
            [
                'name' => 'Orange@!',
                'unit' => 'kg',
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonStringEqualsJsonString(
            '{"errors":[{"status":400,"code":"BAD_REQUEST","message":"Invalid request."}]}',
            $this->client->getResponse()->getContent()
        );
    }
}
