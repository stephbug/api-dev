<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Response;

use Symfony\Component\HttpFoundation\Response;

class ApiRespondToException extends AbstractApiResponse
{
    protected function handleException(\Throwable $exception): Response
    {
        return $this->errorInternalError('Internal error (default message)');
    }
}