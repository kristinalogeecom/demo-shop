<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\Presentation\Controller\AdminController;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use Exception;

try {
    $admin = new AdminController(ServiceRegistry::get('adminService'));

    ServiceRegistry::get('router')->addRoute('GET', '/', function () {
        (new HtmlResponse('Visitor'))->send();
    });

    ServiceRegistry::get('router')->addRoute('GET', '/admin/login', function() {
        (new HtmlResponse('Login', ['errors' => [], 'username' => '']))->send();
    });

    ServiceRegistry::get('router')->addRoute('POST', '/admin/login', function () use ($admin) {
        $request = ServiceRegistry::get('request');

        try {
            ServiceRegistry::get('passwordPolicyMiddleware')->check($request);
            $response = $admin->login($request);
            $response->send();

        } catch (Exception $e) {
            (new HtmlResponse('Login', [
                'errors' => [$e->getMessage()],
                'username' => $request->input('username'),
            ]))->send();
        }
    });

    ServiceRegistry::get('router')->addRoute('GET', '/404', function () {
        (new HtmlResponse('Error404'))->send();
    });
    ServiceRegistry::get('router')->addRoute('GET', '/505', function () {
        (new HtmlResponse('Error505'))->send();
    });

    ServiceRegistry::get('router')->addRoute('GET', '/admin/dashboard', function () {
        $request = ServiceRegistry::get('request');

        ServiceRegistry::get('adminAuthMiddleware')->check($request);

        (new HtmlResponse('AdminDashboard'))->send();
    });

} catch (Exception $e) {

}
