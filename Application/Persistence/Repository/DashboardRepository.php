<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;
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

    public function getFlatCategories()
    {
        return Category::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }


    /**
     * @param CategoryModel $categoryModel
     * @return CategoryModel
     * @throws Exception
     */
    public function saveCategory(CategoryModel $categoryModel): CategoryModel
    {
        $category = $categoryModel->getId()
            ? Category::find($categoryModel->getId())
            : new Category();

        if (!$category) {
            throw new Exception('Category not found');
        }

        $category->fill([
            'parent_id' => $categoryModel->getParentId() !== '' ? $categoryModel->getParentId() : null,
            'name' => $categoryModel->getName(),
            'code' => $categoryModel->getCode(),
            'description' => $categoryModel->getDescription(),
        ])->save();

        $category = $category->fresh();

        return new CategoryModel(
            $category->id,
            $category->parent_id,
            $category->name,
            $category->code,
            $category->description
        );
    }


    /**
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function deleteCategory(int $id): bool
    {
        $category = Category::find($id);

        if (!$category) {
            return false;
        }

        $this->deleteSubcategories($category);

        return $category->delete();
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


    private function deleteSubcategories($category): void
    {
        foreach ($category->children()->get() as $child) {
            $this->deleteSubcategories($child);
            $child->delete();
        }
    }


}