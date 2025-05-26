<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;

interface DashboardServiceInterface
{
    public function getDashboardStats(): array;

    public function getAllCategories(): array;

    public function getCategoryById(int $id): array;

    public function saveCategory(CategoryModel $category): CategoryModel;

    public function deleteCategory(int $id): void;

}