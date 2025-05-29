<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;
use DemoShop\Application\BusinessLogic\RepositoryInterface\CategoryRepositoryInterface;
use DemoShop\Application\BusinessLogic\ServiceInterface\CategoryServiceInterface;
use DemoShop\Application\Persistence\Model\Category;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\NotFoundException;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Exception\ValidationException;


/**
 * Handles business logic related to product categories,
 * including validation, saving, deletion, and retrieval.
 */
class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        $this->categoryRepository = ServiceRegistry::get(CategoryRepositoryInterface::class);
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
     * @param int $id The ID of the category.
     *
     * @return array The category data.
     */
    public function getCategoryById(int $id): array
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
     * @throws ValidationException
     */
    public function saveCategory(CategoryModel $category): CategoryModel
    {
        $this->validateCategory($category);
        return $this->categoryRepository->saveCategory($category);
    }

    /**
     * Deletes a category if it has no products or subcategories with products.
     *
     * @param int $id The ID of the category to delete.
     *
     * @return bool True if deletion is successful.
     *
     * @throws NotFoundException|ValidationException
     */
    public function deleteCategory(int $id): bool
    {
        $category = Category::with('products', 'children')->find($id);

        if (!$category instanceof Category) {
            throw new NotFoundException('Category not found');
        }

        if ($category->hasProducts()) {
            throw new ValidationException("Cannot delete category ' $category->name ' because it has products.");
        }

        $this->checkChildrenForProducts($category);

        return $this->categoryRepository->deleteCategory($id);
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

    /**
     * Retrieves descendant categories IDs in a list.
     *
     * @param int $categoryId
     *
     * @return array The list of categories.
     */
    public function getDescendantIds(int $categoryId): array
    {
        $descendants = [];

        $children = Category::where('parent_id', $categoryId)->get();
        foreach ($children as $child) {
            $descendants[] = $child->id;
            $descendants = array_merge($descendants, $this->getDescendantIds($child->id));
        }

        return $descendants;
    }

    /**
     * Validates category name, code and description.
     *
     * @param CategoryModel $category
     *
     * @return void
     *
     * @throws ValidationException
     */
    private function validateCategory(CategoryModel $category): void
    {
        $this->validateName($category);
        $this->validateCode($category);
        $this->validateDescription($category);
    }

    /**
     * @param CategoryModel $category
     *
     * @return void
     *
     * @throws ValidationException
     */
    private function validateName(CategoryModel $category): void
    {
        $name = trim($category->getName());
        if ($name === '') {
            throw new ValidationException(['Category name is required.']);
        }

        $duplicateName = Category::where('name', $name)
            ->where('parent_id', $category->getParentId())
            ->when(
                $category->getId() !== null,
                fn($q) => $q->where('id', '!=', $category->getId())
            )
            ->exists();

        if ($duplicateName) {
            throw new ValidationException(['Category with this name already exists in this parent category.']);
        }
    }

    /**
     * @param CategoryModel $category
     *
     * @return void
     *
     * @throws ValidationException
     */
    private function validateCode(CategoryModel $category): void
    {
        $code = trim($category->getCode());
        if ($code === '') {
            throw new ValidationException(['Category code is required.']);
        }

        if (!preg_match('/^\d{4}$/', $code)) {
            throw new ValidationException(['Code must be exactly 4 digits.']);
        }

        $duplicateCode = Category::where('code', $code)
            ->when(
                $category->getId() !== null,
                fn($q) => $q->where('id', '!=', $category->getId())
            )
            ->exists();

        if ($duplicateCode) {
            throw new ValidationException(['Code must be unique.']);
        }
    }

    /**
     * @param CategoryModel $category
     *
     * @return void
     *
     * @throws ValidationException
     */
    private function validateDescription(CategoryModel $category): void
    {
        $description = trim($category->getDescription());
        if ($description === '') {
            throw new ValidationException(['Category description is required.']);
        }
    }

    /**
     * Recursively checks subcategories for products.
     *
     * @param Category $category The category whose children will be checked.
     *
     * @return void
     *
     * @throws ValidationException
     */
    private function checkChildrenForProducts(Category $category): void
    {
        foreach ($category->children()->with('products', 'children')->get() as $child) {
            if ($child->hasProducts()) {
                throw new ValidationException("Cannot delete subcategory ' $child->name ' because it has products.");
            }

            $this->checkChildrenForProducts($child);
        }
    }

}