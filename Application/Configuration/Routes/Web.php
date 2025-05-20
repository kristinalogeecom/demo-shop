<?php

require_once __DIR__ . '/../../Presentation/Controller/AuthController.php';

$auth = new AuthController();

/** @var Router $router */
$router->addRoute('GET', '/', function () {
    include __DIR__ . '/../../Presentation/Page/Visitor.phtml';
});

$router->addRoute('GET', '/visitors', function () {
    include __DIR__ . '/../../Presentation/Page/Visitor.phtml';
});

$router->addRoute('GET', '/', function ($request) {
    var_dump($request->url());
    exit;
});

$router->addRoute('GET', '/admin/login', [$auth, 'showLogin']);
$router->addRoute('POST', '/admin/login', [$auth, 'login']);

$router->addRoute('GET', '/404', function () {
    include __DIR__ . '/../../Presentation/Page/Error404.phtml';
});


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