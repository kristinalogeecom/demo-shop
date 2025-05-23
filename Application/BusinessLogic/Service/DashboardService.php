<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\Persistence\Repository\DashboardRepository;
use Exception;

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

    public function getAllCategories(): array
    {
        return $this->dashboardRepository->getCategories();
    }

    /**
     * @throws Exception
     */
    public function getCategoryById($id): array
    {
        return $this->dashboardRepository->getCategoryById($id);
    }



}