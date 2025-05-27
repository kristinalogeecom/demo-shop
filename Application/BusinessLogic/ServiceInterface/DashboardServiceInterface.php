<?php

namespace DemoShop\Application\BusinessLogic\ServiceInterface;

/**
 * Interface for retrieving aggregated admin dashboard statistics.
 */
interface DashboardServiceInterface
{
    /**
     * Retrieves key statistics for display on the admin dashboard.
     *
     * @return array An associative array containing:
     *               - productsCount: int
     *               - categoriesCount: int
     *               - homePageViews: int
     *               - mostViewedProduct: string
     *               - mostViewedProductViews: int
     */
    public function getDashboardStats(): array;

}