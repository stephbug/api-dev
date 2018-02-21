<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Exception;

use StephBug\ApiDev\Exception\Contract\ApiException;

class RuntimeException extends \RuntimeException implements ApiException
{
}