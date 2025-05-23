<?php

namespace DemoShop\Application\Persistence\Repository;

class DashboardRepository
{
    public function getProductsCount():int
    {
        return 5;
    }

    public function getCategoriesCount():int
    {
        return 2;
    }

    public function getHomePageViews(): int
    {
        return 7;
    }

    public function getMostViewedProduct(): string
    {
        return "prod1";
    }

    public function getMostViewedProductViews(): int
    {
        return 3;
    }
}