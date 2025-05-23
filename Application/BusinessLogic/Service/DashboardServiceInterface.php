<?php

namespace DemoShop\Application\BusinessLogic\Service;

interface DashboardServiceInterface
{
    public function getDashboardStats(): array;

    public function getAllCategories(): array;

    public function getCategoryById($id): array;

}