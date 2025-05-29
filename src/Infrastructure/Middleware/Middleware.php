<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Infrastructure\Http\Request;

/**
 * Abstract base class for all middleware components.
 *
 * Implements the Chain of Responsibility pattern,
 * allowing multiple middleware to be linked and executed sequentially.
 */
abstract class Middleware
{
    /**
     * @var Middleware|null The next middleware in the chain
     */
    protected ?Middleware $next = null;

    /**
     * Links the current middleware with the next one in the chain.
     *
     * @param Middleware $next The next middleware to execute
     *
     * @return Middleware The next middleware
     */
    public function linkWith(Middleware $next): Middleware
    {
        $this->next = $next;
        return $next;
    }

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