<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Response;

use Symfony\Component\HttpFoundation\Response;

interface ApiResponse
{
    public function respondTo(\Throwable $exception): Response;
}