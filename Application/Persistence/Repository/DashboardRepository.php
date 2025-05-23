<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\Persistence\Model\Category;
use Exception;

class DashboardRepository
{
    public function getProductsCount():int
    {
        return 10;
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

    public function getCategories(): array
    {
        $categories = Category::withCount('children')
            ->get()
            ->toArray();

        return $this->buildTree($categories);
    }

    /**
     * @throws Exception
     */
    public function getCategoryById($id): array
    {
        $category = Category::with('parent')->find($id);

        if (!$category) {
            throw new Exception('Category not found');
        }

        return $category->toArray();
    }

    private function buildTree(array $categories, $parentId = null): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $branch[] = $category;
            }
        }

        return $branch;
    }

}