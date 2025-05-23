<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\Persistence\Repository\DashboardRepository;

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