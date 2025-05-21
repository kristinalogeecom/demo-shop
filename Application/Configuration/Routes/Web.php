<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\Presentation\Controller\AdminController;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Container\ServiceRegistry;

try {
    $auth = new AdminController(ServiceRegistry::get('adminService'));

    ServiceRegistry::get('router')->addRoute('GET', '/', function () {
        (new HtmlResponse('Visitor'))->send();
    });

    ServiceRegistry::get('router')->addRoute('GET', '/admin/login', [$auth, 'showLogin']);
    ServiceRegistry::get('router')->addRoute('POST', '/admin/login', [$auth, 'login']);

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


//
//              TESTING
//
//$router->addRoute('GET', '/user/{id}', function ($request) {
//    $id = $request->param('id');
//    echo "User ID: $id";
//});
//
//// slug - a part of a URL which identifies a particular page on a website
//
//$router->addRoute('GET', '/post/{slug}/edit', function ($request) {
//    $slug = $request->param('slug');
//    echo "Editing post: $slug";
//});