<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Response;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse as IlluminateJsonResponse;
use Prooph\ServiceBus\Exception\MessageDispatchException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://github.com/ellipsesynergie/api-response/blob/master/src/AbstractResponse.php
 */
abstract class AbstractApiResponse implements ApiResponse
{
    const CODE_WRONG_ARGS = 'GEN-WRONG-ARGS';
    const CODE_NOT_FOUND = 'GEN-NOT-FOUND';
    const CODE_INTERNAL_ERROR = 'GEN-INTERNAL-ERROR';
    const CODE_UNAUTHORIZED = 'GEN-UNAUTHORIZED';
    const CODE_FORBIDDEN = 'GEN-FORBIDDEN';
    const CODE_GONE = 'GEN-GONE';
    const CODE_METHOD_NOT_ALLOWED = 'GEN-METHOD-NOT-ALLOWED';
    const CODE_UNWILLING_TO_PROCESS = 'GEN-UNWILLING-TO-PROCESS';
    const CODE_UNPROCESSABLE = 'GEN-UNPROCESSABLE';

    /**
     * @var Application
     */
    private $app;

    /**
     * @var int
     */
    private $statusCode;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function respondTo(\Throwable $exception): Response
    {
        if ($exception instanceof MessageDispatchException) {
            if ($previousException = $exception->getPrevious()) {
                $exception = $previousException;
            }
        }

        return $this->handleException($exception);
    }

    abstract protected function handleException(\Throwable $exception): Response;

    public function withError(string $message, string $errorCode, array $headers = []): IlluminateJsonResponse
    {
        return $this->withResponse([
            'error' => [
                'code' => $errorCode,
                'http_code' => $this->statusCode,
                'message' => $message
            ]
        ],
            $headers
        );
    }

    public function errorForbidden(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Forbidden';

        return $this->setStatusCode(403)->withError($message, static::CODE_FORBIDDEN, $headers);
    }

    public function errorInternalError(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Internal Error';

        return $this->setStatusCode(500)->withError($message, static::CODE_INTERNAL_ERROR, $headers);
    }

    public function errorNotFound(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Resource Not Found';

        return $this->setStatusCode(404)->withError($message, static::CODE_NOT_FOUND, $headers);
    }

    public function errorUnauthorized(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Unauthorized';

        return $this->setStatusCode(401)->withError($message, static::CODE_UNAUTHORIZED, $headers);
    }

    public function errorWrongArgs(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Wrong Arguments';

        return $this->setStatusCode(400)->withError($message, static::CODE_WRONG_ARGS, $headers);
    }

    public function errorGone(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Resource no longer available';

        return $this->setStatusCode(410)->withError($message, static::CODE_GONE, $headers);
    }

    public function errorMethodNotAllowed(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Method Not Allowed';

        return $this->setStatusCode(405)->withError($message, static::CODE_METHOD_NOT_ALLOWED, $headers);
    }

    public function errorUnwillingToProcess(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Server is unwilling to process the request';

        return $this->setStatusCode(431)->withError($message, static::CODE_UNWILLING_TO_PROCESS, $headers);
    }

    public function errorUnprocessable(string $message = null, array $headers = []): IlluminateJsonResponse
    {
        $message = $message ?? 'Unprocessable Entity';

        return $this->setStatusCode(422)->withError($message, static::CODE_UNPROCESSABLE, $headers);
    }

    protected function withResponse(array $data, array $headers): IlluminateJsonResponse
    {
        return new IlluminateJsonResponse($data, $this->statusCode, $headers);
    }

    protected function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function isDevEnvironment(): bool
    {
        return $this->app->environment() !== 'production';
    }
}