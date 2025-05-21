<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\Presentation\Controller\AdminController;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Container\ServiceRegistry;

try {
    $admin = new AdminController(ServiceRegistry::get('adminService'));

    ServiceRegistry::get('router')->addRoute('GET', '/', function () {
        (new HtmlResponse('Visitor'))->send();
    });

    ServiceRegistry::get('router')->addRoute('GET', '/admin/login', [$admin, 'showLogin']);
    ServiceRegistry::get('router')->addRoute('POST', '/admin/login', [$admin, 'login']);

    ServiceRegistry::get('router')->addRoute('GET', '/404', function () {
        (new HtmlResponse('Error404'))->send();
    });
    ServiceRegistry::get('router')->addRoute('GET', '/505', function () {
        (new HtmlResponse('Error505'))->send();
    });

    ServiceRegistry::get('router')->addRoute('GET', '/admin/dashboard', function () {
        (new HtmlResponse('AdminDashboard'))->send();
    });

} catch (\Exception $e) {

}
