<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\RepositoryInterface\ProductRepositoryInterface;
use DemoShop\Application\BusinessLogic\ServiceInterface\ProductServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;

class ProductService implements ProductServiceInterface
{
    private ProductRepositoryInterface $productRepository;

    /**
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        $this->productRepository = ServiceRegistry::get(ProductRepositoryInterface::class);
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->getAllProducts();
    }
}