<?php

namespace DemoShop\Application\Configuration;

use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;
use DemoShop\Application\BusinessLogic\RepositoryInterface\AdminTokenRepositoryInterface;
use DemoShop\Application\BusinessLogic\RepositoryInterface\AuthenticationRepositoryInterface;
use DemoShop\Application\BusinessLogic\RepositoryInterface\CategoryRepositoryInterface;
use DemoShop\Application\BusinessLogic\RepositoryInterface\DashboardRepositoryInterface;
use DemoShop\Application\BusinessLogic\Service\AuthenticationService;
use DemoShop\Application\BusinessLogic\Service\CategoryService;
use DemoShop\Application\BusinessLogic\Service\DashboardService;
use DemoShop\Application\BusinessLogic\ServiceInterface\AuthenticationServiceInterface;
use DemoShop\Application\BusinessLogic\ServiceInterface\CategoryServiceInterface;
use DemoShop\Application\BusinessLogic\ServiceInterface\DashboardServiceInterface;
use DemoShop\Application\Configuration\Routes\WebRouteRegistrar;
use DemoShop\Application\Persistence\Encryption\Encrypter;
use DemoShop\Application\Persistence\Repository\AdminTokenRepository;
use DemoShop\Application\Persistence\Repository\AuthenticationRepository;
use DemoShop\Application\Persistence\Repository\CategoryRepository;
use DemoShop\Application\Persistence\Repository\DashboardRepository;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\InvalidArgumentException;
use DemoShop\Infrastructure\Exception\NotFoundException;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use DemoShop\Infrastructure\Middleware\RedirectIfAuthenticatedMiddleware;
use DemoShop\Infrastructure\Router\RouteDispatcher;
use DemoShop\Infrastructure\Security\CookieManager;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Application bootstrapper responsible for initializing core components.
 *
 * Handles:
 * - Environment loading
 * - Database configuration
 * - Dependency injection setup
 * - Route registration and dispatching
 */
class App
{
    /**
     * Boots the application by initializing database, services, and router.
     *
     * @throws Exception If any initialization step fails.
     *
     * @return void
     */
    public static function boot(): void
    {
        self::initDatabase();
        self::initServices();
        self::initRouter();
    }

    /**
     * Initializes the database connection using Eloquent ORM.
     *
     * Loads environment variables from the .env file and sets
     * up the database connection parameters for Eloquent.
     *
     * @throws NotFoundException If the .env file is missing or connection setup fails.
     *
     * @return void
     */
    private static function initDatabase(): void
    {
        $envPath = realpath(__DIR__ . '/../../../');
        if (!file_exists($envPath . '/.env')) {
            throw new NotFoundException('.env file is missing.');
        }

        $dotenv = Dotenv::createImmutable($envPath);
        $dotenv->load();

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver'    => $_ENV['DB_CONNECTION'],
            'host'      => $_ENV['DB_HOST'],
            'database'  => $_ENV['DB_DATABASE'],
            'username'  => $_ENV['DB_USERNAME'],
            'password'  => $_ENV['DB_PASSWORD'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }


    /**
     * Registers services and dependencies in the application's service container.
     *
     * @return void
     *
     * @throws InvalidArgumentException if encryption key is not set
     */
    private static function initServices(): void
    {
        ServiceRegistry::set(Request::class, new Request());

        ServiceRegistry::set(AdminAuthMiddleware::class, new AdminAuthMiddleware());
        ServiceRegistry::set(PasswordPolicyMiddleware::class, new PasswordPolicyMiddleware());
        ServiceRegistry::set(RedirectIfAuthenticatedMiddleware::class, new RedirectIfAuthenticatedMiddleware());


        $rawKey = $_ENV['ENCRYPTION_KEY'];
        if (!$rawKey) {
            throw new InvalidArgumentException('Encryption key.');
        }

        $encryption = new Encrypter($rawKey);
        ServiceRegistry::set(EncryptionInterface::class, $encryption);

        ServiceRegistry::set(CookieManager::class, new CookieManager($encryption));

        // Repositories
        ServiceRegistry::set(AuthenticationRepositoryInterface::class, new AuthenticationRepository($encryption));
        ServiceRegistry::set(AdminTokenRepositoryInterface::class, new AdminTokenRepository());
        ServiceRegistry::set(DashboardRepositoryInterface::class, new DashboardRepository());
        ServiceRegistry::set(CategoryRepositoryInterface::class, new CategoryRepository());

        // Services
        ServiceRegistry::set(AuthenticationServiceInterface::class, new AuthenticationService());
        ServiceRegistry::set(DashboardServiceInterface::class, new DashboardService());
        ServiceRegistry::set(CategoryServiceInterface::class, new CategoryService());
    }


    /**
     * Initializes the application's router and dispatches the incoming request.
     *
     * @return void
     *
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    private static function initRouter(): void
    {
        $request = ServiceRegistry::get(Request::class);
        $dispatcher = new RouteDispatcher($request);
        WebRouteRegistrar::register($dispatcher);
        $dispatcher->dispatch();
    }

}
