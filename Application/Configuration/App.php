<?php

namespace DemoShop\Application\Configuration;

use DemoShop\Application\Persistence\Repository\AdminRepository;
use DemoShop\Application\Presentation\Controller\AdminController;
use Illuminate\Database\Capsule\Manager as Capsule;
use DemoShop\Infrastructure\Router\Router;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Application\BusinessLogic\Service\AdminService;
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
        self::initDatabase();
        self::initServices();
        self::initRouter();
    }

    /**
     * just for testing connection to the database
     *
     * @throws Exception
     */
    public static function bootWithoutRouter(): void
    {
        self::initDatabase();
        self::initServices();
    }


    private static function initDatabase(): void
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'demo_shop',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

    }

    /**
     * @throws Exception
     */
    private static function initServices(): void
    {
        ServiceRegistry::set('router', new Router());
        ServiceRegistry::set('request', new Request());
        ServiceRegistry::set('adminRepository', new AdminRepository());

        ServiceRegistry::set('adminService', new AdminService(
            ServiceRegistry::get("adminRepository")
        ));

        ServiceRegistry::set('adminController', new AdminController(
            ServiceRegistry::get("adminService")
        ));
    }

    /**
     * @throws Exception
     */
    private static function initRouter(): void
    {
        $router = ServiceRegistry::get('router');
        include __DIR__ . '/Routes/Web.php';
        $router->matchRoute(ServiceRegistry::get('request'));
    }
}