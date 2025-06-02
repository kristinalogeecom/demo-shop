<?php

namespace DemoShop\Application\BusinessLogic\RepositoryInterface;

use DemoShop\Application\BusinessLogic\DTO\CategoryModel;

/**
 * Interface for managing product categories.
 */
interface CategoryRepositoryInterface
{
    /**
     * Retrieves the full category tree.
     *
     * @return array The hierarchical list of categories.
     */
    public function getCategories(): array;

    /**
     * Retrieves a single category by its ID.
     *
     * @param int $id The ID of the category.
     *
     * @return array The category data as an associative array.
     */
    public function getCategoryById(int $id): array;

    /**
     * Retrieves a flat list of all categories (no hierarchy).
     *
     * @return array The list of categories.
     */
    public function getFlatCategories(): array;

    /**
     * Saves or updates a category.
     *
     * @param CategoryModel $categoryModel The category model to save.
     *
     * @return CategoryModel The saved category model (with ID if newly created).
     */
    public function saveCategory(CategoryModel $categoryModel): CategoryModel;

    /**
     * Deletes a category by ID.
     *
     * @param int $id The ID of the category to delete.
     *
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteCategory(int $id): bool;
}