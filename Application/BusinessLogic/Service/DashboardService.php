<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\Persistence\Model\Category;
use DemoShop\Application\BusinessLogic\Model\CategoryModel;
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

    /**
     * @throws Exception
     */
    public function saveCategory(CategoryModel $category): CategoryModel
    {
        $name = trim($category->getName());

        if ($name === '') {
            throw new Exception('Category name is required');
        }

        $duplicateExists = Category::where('name', $category->getName())
            ->where('parent_id', $category->getParentId())
            ->when(
                $category->getId() !== null,
                fn($q) => $q->where('id', '!=', $category->getId())
            )
            ->exists();

        if ($duplicateExists) {
            throw new Exception('Category with this name already exists in this parent category');
        }

        return $this->dashboardRepository->saveCategory($category);
    }


    /**
     * @throws Exception
     */
    public function deleteCategory(int $id): bool
    {
        $category = Category::with('products', 'children')->find($id);

        if (!$category || !$category instanceof Category) {
            throw new Exception('Category not found');
        }

        if ($category->hasProducts()) {
            throw new Exception("Cannot delete category '{$category->name}' because it has products.");
        }

        $this->checkChildrenForProducts($category);

        return $this->dashboardRepository->deleteCategory($id);
    }

    /**
     * @param Category $category
     * @return void
     * @throws Exception
     */
    private function checkChildrenForProducts(Category $category): void
    {
        foreach ($category->children()->with('products', 'children')->get() as $child) {
            if ($child->hasProducts()) {
                throw new Exception("Cannot delete subcategory '{$child->name}' because it has products.");
            }

            $this->checkChildrenForProducts($child);
        }
    }

    public function getFlatCategories(): array
    {
        return $this->dashboardRepository->getFlatCategories();
    }
}