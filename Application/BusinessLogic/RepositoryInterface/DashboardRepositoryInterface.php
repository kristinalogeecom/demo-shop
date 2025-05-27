<?php

namespace DemoShop\Application\BusinessLogic\RepositoryInterface;

/**
 * Interface for retrieving dashboard statistics.
 */
interface DashboardRepositoryInterface
{
    /**
     * Gets the total number of products.
     *
     * @return int The number of products.
     */
    public function getProductsCount(): int;

    /**
     * Gets the total number of categories.
     *
     * @return int The number of categories.
     */
    public function getCategoriesCount(): int;

    /**
     * Gets the number of home page views.
     *
     * @return int The number of times the home page was viewed.
     */
    public function getHomePageViews(): int;

    /**
     * Gets the name of the most viewed product.
     *
     * @return string The product name.
     */
    public function getMostViewedProduct(): string;

    /**
     * Gets the number of views for the most viewed product.
     *
     * @return int The view count.
     */
    public function getMostViewedProductViews(): int;
}