<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Infrastructure\Http\Request;

abstract class Middleware
{
    protected ?Middleware $next = null;

    public function linkWith(Middleware $next): Middleware
    {
        $this->next = $next;
        return $next;
    }

    public function check(Request $request): void
    {
        $this->handle($request);

        $this->next?->check($request);
    }

    abstract protected function handle(Request $request): void;

}