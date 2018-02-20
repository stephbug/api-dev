<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Exception;

use StephBug\ApiDev\Exception\Contract\ApiException;

class ApiDevException extends \RuntimeException implements ApiException
{
}