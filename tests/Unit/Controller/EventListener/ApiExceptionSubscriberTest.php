<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\EventListener;

use App\Controller\EventListener\ApiExceptionSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class ApiExceptionSubscriberTest extends TestCase
{
    private ApiExceptionSubscriber $subscriber;
    private KernelInterface $kernel;

    protected function setUp(): void
    {
        $this->subscriber = new ApiExceptionSubscriber();
        $this->kernel = $this->createStub(KernelInterface::class);
    }

    #[Test]
    public function shouldSubscribeToExceptionEvent(): void
    {
        $events = iterator_to_array($this->subscriber::getSubscribedEvents());

        $this->assertCount(1, $events);
        $this->assertArrayHasKey(ExceptionEvent::class, $events);
        $this->assertSame('onKernelException', $events[ExceptionEvent::class]);
    }

    #[Test]
    public function shouldHandleApiRequestWithBadRequestException(): void
    {
        $request = Request::create('/api/v1/fruits');
        $exception = new BadRequestHttpException('Invalid input data');

        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->subscriber->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertSame([
            'errors' => [
                [
                    'status' => 400,
                    'code' => 'BAD_REQUEST',
                    'message' => 'Invalid request.',
                ],
            ],
        ], $content);
    }

    #[Test]
    public function shouldHandleApiRequestWithNotFoundException(): void
    {
        $request = Request::create('/api/v1/fruits/999');
        $exception = new NotFoundHttpException('Fruit not found');

        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->subscriber->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertSame([
            'errors' => [
                [
                    'status' => 404,
                    'code' => 'NOT_FOUND',
                    'message' => 'Item not found.',
                ],
            ],
        ], $content);
    }

    #[Test]
    public function shouldHandleApiRequestWithGenericException(): void
    {
        $request = Request::create('/api/v1/fruits', Request::METHOD_POST);
        $exception = new \RuntimeException('Database connection failed');

        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->subscriber->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertSame([
            'errors' => [
                [
                    'status' => 500,
                    'code' => 'INTERNAL_SERVER_ERROR',
                    'message' => 'An internal error occurred.',
                ],
            ],
        ], $content);
    }

    #[Test]
    public function shouldIgnoreNonApiRequest(): void
    {
        $request = Request::create('/web/dashboard');
        $exception = new BadRequestHttpException('Some error');

        $event = new ExceptionEvent(
            $this->kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->subscriber->onKernelException($event);

        $this->assertNull($event->getResponse());
    }
}
