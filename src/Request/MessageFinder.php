<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Request;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MessageFinder
{
    /**
     * @var Collection
     */
    private $messages;

    public function __construct(Collection $messages)
    {
        $this->messages = $messages;
    }

    public function findMessageIn(Request $request): ?string
    {
        $routeName = $this->findRouteName($request);

        if (!$routeName) {
            return null;
        }

        return $this->messages->get($routeName);
    }

    protected function findRouteName(Request $request): ?string
    {
        return $request->route()->getName();
    }
}