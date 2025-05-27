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
use DemoShop\Application\Persistence\Repository\AuthenticationRepository;
use DemoShop\Application\Persistence\Repository\AdminTokenRepository;
use DemoShop\Application\Persistence\Repository\CategoryRepository;
use DemoShop\Application\Persistence\Repository\DashboardRepository;
use DemoShop\Application\Presentation\Controller\AuthenticationController;
use DemoShop\Application\Presentation\Controller\CategoryController;
use DemoShop\Application\Presentation\Controller\DashboardController;
use DemoShop\Application\Presentation\Controller\ProductController;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use DemoShop\Infrastructure\Router\Router;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * The main entry point of the application.
 * Responsible for initializing core services,
 * setting up the database connection and dispatching HTTP request
 */
class App
{
    /**
     * Boots the application by initializing the database,
     * registering services, and loading routes.
     *
     * @return void
     * @throws Exception
     */
    public static function boot(): void
    {
        self::initDatabase();
        self::initServices();
        self::initRouter();
    }

    /**
     * just for testing
     *
     * @throws Exception
     */
    public static function bootWithoutRouter(): void
    {
        self::initDatabase();
        self::initServices();
    }


    /**
     * Loads environment variables and initializes
     * the database connection using EloquentORM.
     *
     * @return void
     * @throws Exception
     */
    private static function initDatabase(): void
    {

        $envPath = realpath(__DIR__ . '/../../');
        if (!file_exists($envPath . '/.env')) {
            throw new Exception('.env file is missing.');
        }

        $dotenv = Dotenv::createImmutable(realpath(__DIR__ . '/../../'));
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
     * Registers core services.
     *
     * @return void
     * @throws Exception
     */
    private static function initServices(): void
    {
        ServiceRegistry::set(Router::class, new Router());
        ServiceRegistry::set(Request::class, new Request());

        ServiceRegistry::set(AdminAuthMiddleware::class, new AdminAuthMiddleware());
        ServiceRegistry::set(PasswordPolicyMiddleware::class, new PasswordPolicyMiddleware());

        $rawKey = $_ENV['ENCRYPTION_KEY'];
        if (!$rawKey) {
            throw new Exception('Encryption key not set.');
        }

        $encryption = new Encrypter($rawKey);
        ServiceRegistry::set(EncryptionInterface::class, $encryption);

        // Repositories
        $authRepository = new AuthenticationRepository($encryption);
        ServiceRegistry::set(AuthenticationRepositoryInterface::class, $authRepository);

        $adminTokenRepository = new AdminTokenRepository();
        ServiceRegistry::set(AdminTokenRepositoryInterface::class, $adminTokenRepository);

        $dashboardRepository = new DashboardRepository();
        ServiceRegistry::set(DashboardRepositoryInterface::class, $dashboardRepository);

        $categoryRepository = new CategoryRepository();
        ServiceRegistry::set(CategoryRepositoryInterface::class, $categoryRepository);

        // Services
        $authService = new AuthenticationService($authRepository, $adminTokenRepository);
        ServiceRegistry::set(AuthenticationServiceInterface::class, $authService);

        $dashboardService = new DashboardService($dashboardRepository);
        ServiceRegistry::set(DashboardServiceInterface::class, $dashboardService);

        $categoryService = new CategoryService($categoryRepository);
        ServiceRegistry::set(CategoryServiceInterface::class, $categoryService);

        // Controllers
        ServiceRegistry::set(AuthenticationController::class, new AuthenticationController($authService));
        ServiceRegistry::set(DashboardController::class, new DashboardController($dashboardService));
        ServiceRegistry::set(CategoryController::class, new CategoryController($categoryService));
        ServiceRegistry::set(ProductController::class, new ProductController());

    }

    /**
     * Loads application routes from the WebRouteRegistrar.php file
     * and dispatches the current HTTP request using the router.
     *
     * @return void
     * @throws Exception
     */
    private static function initRouter(): void
    {
        $router = ServiceRegistry::get(Router::class);
        WebRouteRegistrar::register($router);
        $router->matchRoute(ServiceRegistry::get(Request::class));
    }
}