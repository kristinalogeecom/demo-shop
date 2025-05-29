<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Infrastructure\Http\Request;

/**
 * Abstract base class for all middleware components.
 *
 */
abstract class Middleware
{
    /**
     * @var Middleware|null The next middleware in the chain
     */
    protected ?Middleware $next = null;

    /**
     * Executes the current middleware and passes control to the next one if available.
     *
     * @param Request $request
     * @return void
     */
    public function check(Request $request): void
    {
        $this->handle($request);

        // Continue to the next middleware in the chain, if any
        $this->next?->check($request);
    }

    /**
     *  Handles the logic for this middleware.
     *  Must be implemented by concrete subclasses.
     *
     * @param Request $request
     *
     * @return void
     */
    abstract protected function handle(Request $request): void;

}