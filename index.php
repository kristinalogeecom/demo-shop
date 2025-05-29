<?php

require_once __DIR__ . '/vendor/autoload.php';

use DemoShop\Application\Configuration\App;
use DemoShop\Infrastructure\Exception\ResponseException;
use DemoShop\Infrastructure\Exception\NotFoundException;
use DemoShop\Infrastructure\Exception\ValidationException;
use DemoShop\Infrastructure\Exception\EncryptionException;
use DemoShop\Infrastructure\Exception\DecryptionException;
use DemoShop\Infrastructure\Response\HtmlResponse;

try {
    App::boot();
} catch (ResponseException $e) {
    $e->getResponse()->send();
} catch (ValidationException $e) {
    $response = new HtmlResponse('ErrorValidation', [
        'errorMessage' => implode('<br>', $e->getErrors()),
    ], 422);
    $response->send();
} catch (EncryptionException|DecryptionException $e) {
    $response = new HtmlResponse('ErrorEncryption', [
        'errorMessage' => 'An error occurred during encryption or decryption.',
    ], 500);
    $response->send();
} catch (NotFoundException $e) {
    $response = new HtmlResponse('Error404', [
        'errorMessage' => 'Page not found.',
    ], 404);
    $response->send();
} catch (Exception $e) {
    $response = new HtmlResponse('Error', [
        'errorMessage' => 'An unexpected error occurred.',
        'details' => $e->getMessage(),
    ], 500);
    $response->send();
}
