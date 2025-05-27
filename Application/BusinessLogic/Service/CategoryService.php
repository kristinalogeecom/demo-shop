<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;
use DemoShop\Application\BusinessLogic\RepositoryInterface\CategoryRepositoryInterface;
use DemoShop\Application\BusinessLogic\ServiceInterface\CategoryServiceInterface;
use DemoShop\Application\Persistence\Model\Category;
use Exception;


/**
 * Handles business logic related to product categories,
 * including validation, saving, deletion, and retrieval.
 */
class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $categoryRepository;


    /**
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Retrieves the full category tree.
     *
     * @return array The hierarchical list of categories.
     */
    public function getAllCategories(): array
    {
        return $this->categoryRepository->getCategories();
    }

    /**
     * Retrieves category data by ID.
     *
     * @param mixed $id The ID of the category.
     *
     * @return array The category data.
     *
     * @throws Exception If the category is not found.
     */
    public function getCategoryById($id): array
    {
        return $this->categoryRepository->getCategoryById($id);
    }


    /**
     * Validates and saves a category.
     *
     * @param CategoryModel $category The category model to save.
     *
     * @return CategoryModel The saved category.
     *
     * @throws Exception If the category is invalid or a duplicate exists.
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

        return $this->categoryRepository->saveCategory($category);
    }


    /**
     * Deletes a category if it has no products or subcategories with products.
     *
     * @param int $id The ID of the category to delete.
     *
     * @return bool True if deletion is successful.
     *
     * @throws Exception If the category has products or is not found.
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

        return $this->categoryRepository->deleteCategory($id);
    }

    /**
     * Recursively checks subcategories for products.
     *
     * @param Category $category The category whose children will be checked.
     *
     * @return void
     *
     * @throws Exception If any child category contains products.
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

    /**
     * Retrieves all categories in a flat list (no hierarchy).
     *
     * @return array The list of categories.
     */
    public function getFlatCategories(): array
    {
        return $this->categoryRepository->getFlatCategories();
    }
}