<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\RepositoryInterface\DashboardRepositoryInterface;
use DemoShop\Application\BusinessLogic\ServiceInterface\DashboardServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;

/**
 * Handles business logic for retrieving dashboard-related statistics.
 */
class DashboardService implements DashboardServiceInterface
{
    private DashboardRepositoryInterface $dashboardRepository;

    /**
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        $this->dashboardRepository = ServiceRegistry::get(DashboardRepositoryInterface::class);
    }

    /**
     * Retrieves all statistics needed for the admin dashboard.
     *
     * @return array An associative array containing:
     *               - productsCount: int
     *               - categoriesCount: int
     *               - homePageViews: int
     *               - mostViewedProduct: string
     *               - mostViewedProductViews: int
     */
    public function getDashboardStats(): array
    {
        return [
            'productsCount' => $this->dashboardRepository->getProductsCount(),
            'categoriesCount' => $this->dashboardRepository->getCategoriesCount(),
            'homePageViews' => $this->dashboardRepository->getHomePageViews(),
            'mostViewedProduct' => $this->dashboardRepository->getMostViewedProduct(),
            'mostViewedProductViews' => $this->dashboardRepository->getMostViewedProductViews(),
        ];
    }

}