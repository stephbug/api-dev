<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Response;

use Illuminate\Http\JsonResponse as IlluminateJsonResponse;
use React\Promise\PromiseInterface;
use Symfony\Component\HttpFoundation\Response;

class JsonResponse implements ResponseStrategy
{
    public function fromPromise(PromiseInterface $promise): Response
    {
        $json = null;

        $promise->then(function ($data) use (&$json) {
            $json = $data;
        });

        return new IlluminateJsonResponse($json);
    }

    public function withStatus(int $statusCode): Response
    {
        return new IlluminateJsonResponse([], $statusCode);
    }
}