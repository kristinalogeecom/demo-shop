<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\BusinessLogic\RepositoryInterface\DashboardRepositoryInterface;
use DemoShop\Application\Persistence\Model\Category;

/**
 * Repository implementation for retrieving admin dashboard statistics.
 */
class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Gets the total number of products.
     *
     * @return int The number of products.
     */
    public function getProductsCount():int
    {
        return 10;
    }

    /**
     * Gets the total number of categories.
     *
     * @return int The number of categories.
     */
    public function getCategoriesCount():int
    {
        return Category::count();
    }

    /**
     * Gets the number of times the home page has been viewed.
     *
     * @return int The number of home page views.
     */
    public function getHomePageViews(): int
    {
        return 7;
    }

    /**
     * Gets the name of the most viewed product.
     *
     * @return string The product name.
     */
    public function getMostViewedProduct(): string
    {
        return "prod1";
    }

    /**
     * Gets the view count for the most viewed product.
     *
     * @return int The number of views.
     */
    public function getMostViewedProductViews(): int
    {
        return 3;
    }

}