<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\ServiceInterface\DashboardServiceInterface;
use DemoShop\Application\Persistence\Repository\DashboardRepository;

/**
 * Handles business logic for retrieving dashboard-related statistics.
 */
class DashboardService implements DashboardServiceInterface
{
    private DashboardRepository $dashboardRepository;

    /**
     * @param $dashboardRepository
     */
    public function __construct($dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
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