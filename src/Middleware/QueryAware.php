<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Middleware;

use Illuminate\Http\Request;

class QueryAware
{
    public function handle(Request $request, \Closure $next)
    {
        if (null !== $message = $request->get($this->getMessageAttribute())) {
            $request->request->add([$this->getMessageAttribute() => $message]);
        }

        return $next($request);
    }

    public function getMessageAttribute(): string
    {
        return QueryMessage::NAME_ATTRIBUTE;
    }
}