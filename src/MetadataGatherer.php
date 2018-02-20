<?php

declare(strict_types=1);

namespace StephBug\ApiDev;

use Illuminate\Http\Request;

interface MetadataGatherer
{
    public function fromRequest(Request $request): array;
}