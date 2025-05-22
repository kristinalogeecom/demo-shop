<?php

namespace DemoShop\Application\Configuration;

use DemoShop\Application\Persistence\Repository\AdminRepository;
use DemoShop\Application\Presentation\Controller\AdminController;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use Illuminate\Database\Capsule\Manager as Capsule;
use DemoShop\Infrastructure\Router\Router;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Application\BusinessLogic\Service\AdminService;
use DemoShop\Application\Persistence\Encryption\Encrypter;
use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;
use Dotenv\Dotenv;
use Exception;

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
     */
    private static function initDatabase(): void
    {
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
        ServiceRegistry::set('router', new Router());
        ServiceRegistry::set('request', new Request());

        ServiceRegistry::set('adminAuthMiddleware', new AdminAuthMiddleware());
        ServiceRegistry::set('passwordPolicyMiddleware', new PasswordPolicyMiddleware());

        $rawKey = $_ENV['ENCRYPTION_KEY'];
        $encryption = new Encrypter($rawKey);
        ServiceRegistry::set(EncryptionInterface::class, $encryption);

        ServiceRegistry::set('adminRepository', new AdminRepository(
            ServiceRegistry::get(EncryptionInterface::class),
        ));

        ServiceRegistry::set('adminService', new AdminService(
            ServiceRegistry::get("adminRepository")
        ));

        ServiceRegistry::set('adminController', new AdminController(
            ServiceRegistry::get("adminService")
        ));
    }

    /**
     * Loads application routes from the Web.php file
     * and dispatches the current HTTP request using the router.
     *
     * @return void
     * @throws Exception
     */
    private static function initRouter(): void
    {
        $router = ServiceRegistry::get('router');
        include __DIR__ . '/Routes/Web.php';
        $router->matchRoute(ServiceRegistry::get('request'));
    }
}