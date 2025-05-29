<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;
use DemoShop\Application\BusinessLogic\RepositoryInterface\CategoryRepositoryInterface;
use DemoShop\Application\Persistence\Model\Category;
use Exception;

/**
 * Repository implementation for managing product categories,
 * including hierarchical retrieval, saving, and deletion logic.
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Retrieves all categories with child count and builds a hierarchical tree.
     *
     * @return array The category tree.
     */
    public function getCategories(): array
    {
        $categories = Category::withCount('children')
            ->get()
            ->toArray();

        return $this->buildTree($categories);
    }

    /**
     * Retrieves a single category by ID, including its parent.
     *
     * @param int $id The category ID.
     *
     * @return array The category data as an associative array.
     *
     * @throws Exception If the category is not found.
     */
    public function getCategoryById(int $id): array
    {
        $category = Category::with('parent')->find($id);

        if (!$category) {
            throw new Exception('Category not found');
        }

        return $category->toArray();
    }

    /**
     * Retrieves a flat (non-nested) list of categories, sorted by name.
     *
     * @return array Flat list of categories with ID and name.
     */
    public function getFlatCategories(): array
    {
        return Category::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Creates or updates a category based on the provided model.
     *
     * @param CategoryModel $categoryModel The category data.
     *
     * @return CategoryModel The saved category as a model instance.
     *
     * @throws Exception If updating and the category does not exist.
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
     * Deletes a category and its subcategories recursively.
     *
     * @param int $id The ID of the category to delete.
     *
     * @return bool True if deletion succeeded, false otherwise.
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

    /**
     * Builds a hierarchical tree from a flat category array.
     *
     * @param array $categories The flat array of categories.
     * @param int|null $parentId The current parent ID.
     *
     * @return array The built tree.
     */
    private function buildTree(array $categories, int $parentId = null): array
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

    /**
     * Recursively deletes all child categories of the given category.
     *
     * @param Category $category The category whose children should be deleted.
     *
     * @return void
     */
    private function deleteSubcategories(Category $category): void
    {
        foreach ($category->children()->get() as $child) {
            $this->deleteSubcategories($child);
            $child->delete();
        }
    }
}
