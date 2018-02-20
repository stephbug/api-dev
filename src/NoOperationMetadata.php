<?php

declare(strict_types=1);

namespace StephBug\ApiDev;

use Illuminate\Http\Request;

class NoOperationMetadata implements MetadataGatherer
{
    public function fromRequest(Request $request): array
    {
        return [];
    }
}