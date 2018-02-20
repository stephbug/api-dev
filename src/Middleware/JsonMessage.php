<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use StephBug\ApiDev\Exception\ApiDevException;
use Symfony\Component\HttpFoundation\Response;

class JsonMessage
{
    public function handle(Request $request, \Closure $next)
    {
        try {
            $request->expectsJson() and $this->setJsonOnRequest($request);

            return $next($request);
        } catch (\Throwable $exception) {
            return $this->onException($request, $exception);
        }
    }

    private function setJsonOnRequest(Request $request): void
    {
        $payload = (array) json_decode($request->getContent(), true);

        $this->onJsonError();

        $request->setJson($payload);
    }

    private function onJsonError(): void
    {
        $statusCode = Response::HTTP_BAD_REQUEST;

        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new ApiDevException('Invalid JSON, maximum stack depth exceeded.', $statusCode);
            case JSON_ERROR_UTF8:
                throw new ApiDevException('Malformed UTF-8 characters, possibly incorrectly encoded.', $statusCode);
            case JSON_ERROR_SYNTAX:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_STATE_MISMATCH:
                throw new ApiDevException('Invalid JSON.', $statusCode);
        }
    }

    private function onException(Request $request, \Throwable $exception): Response
    {
        if ($request->expectsJson()) {
            return new JsonResponse([
                'data' => [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString()
                ]
            ]);
        }

        throw $exception;
    }
}