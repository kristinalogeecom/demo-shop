<?php

namespace DemoShop\Application\Configuration;

use DemoShop\Infrastructure\Router\Router;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use Exception;

/**
 * The main application class responsible for bootstrapping
 * services and dispatching the current HTTP request.
 */
class App
{
    /**
     * Initializes and registers core services.
     *
     * @return void
     * @throws Exception
     */
    public static function boot(): void
    {
        ServiceRegistry::set('router', new Router());
        ServiceRegistry::set('request', new Request());

        $router = ServiceRegistry::get('router');
        include __DIR__ . '/Routes/Web.php';

        $router->matchRoute(ServiceRegistry::get('request'));
    }
}