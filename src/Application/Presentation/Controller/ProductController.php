<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\ServiceInterface\ProductServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\Response;

/**
 * Handles HTTP requests related to product management in the admin panel.
 */
class ProductController
{
    public function getProductsHtml(): HtmlResponse
    {
        return new HtmlResponse('Products');
    }

    /**
     * Returns a list of products.
     * (Currently hardcoded — intended to be replaced with dynamic data.)
     *
     * @param Request $request
     * @return Response JSON response containing an array of products.
     */
    public function getProducts(Request $request): Response
    {
        try {
            $products = $this->getProductService()->getAllProducts();
            return new JsonResponse($products);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    private function getProductService(): ProductServiceInterface
    {
        return ServiceRegistry::get(ProductServiceInterface::class);
    }
}
