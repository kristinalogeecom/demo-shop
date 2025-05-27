<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\Response;

/**
 * Handles HTTP requests related to product management in the admin panel.
 */
class ProductController
{
    /**
     * Returns a list of products.
     * (Currently hardcoded — intended to be replaced with dynamic data.)
     *
     * @return Response JSON response containing an array of products.
     */
    public function getProducts(): Response
    {
        return new JsonResponse([
            ['id' => 1, 'name' => 'Product 1', 'price' => 10.99],
            ['id' => 2, 'name' => 'Product 2', 'price' => 20.50]
        ]);
    }
}
