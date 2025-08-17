<?php

declare(strict_types=1);

namespace App\Controller\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private const ERROR_CODES = [
        Response::HTTP_BAD_REQUEST => 'BAD_REQUEST',
        Response::HTTP_NOT_FOUND => 'NOT_FOUND',
        Response::HTTP_INTERNAL_SERVER_ERROR => 'INTERNAL_SERVER_ERROR',
    ];

    private const ERROR_MESSAGES = [
        Response::HTTP_BAD_REQUEST => 'Invalid request.',
        Response::HTTP_NOT_FOUND => 'Item not found.',
        Response::HTTP_INTERNAL_SERVER_ERROR => 'An internal error occurred.',
    ];

    public static function getSubscribedEvents(): \Generator
    {
        yield ExceptionEvent::class => 'onKernelException';
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (
            !$this->isApiRequest($event->getRequest()->getRequestUri())
        ) {
            return;
        }

        $exception = $event->getThrowable();
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        }

        $event->setResponse(new JsonResponse($this->buildErrorResponse($statusCode), $statusCode));
    }

    private function isApiRequest(string $requestUri): bool
    {
        return str_starts_with($requestUri, '/api/');
    }

    private function buildErrorResponse(int $code): array
    {
        return [
            'errors' => [
                [
                    'status' => $code,
                    'code' => self::ERROR_CODES[$code] ?? self::ERROR_CODES[Response::HTTP_INTERNAL_SERVER_ERROR],
                    'message' => self::ERROR_MESSAGES[$code] ?? self::ERROR_MESSAGES[Response::HTTP_INTERNAL_SERVER_ERROR],
                ],
            ],
        ];
    }
}
