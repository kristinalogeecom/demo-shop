<?php

namespace DemoShop\Application\BusinessLogic\ServiceInterface;

use DemoShop\Application\BusinessLogic\Model\CategoryModel;

/**
 * Interface for handling business logic related to product categories.
 */
interface CategoryServiceInterface
{
    /**
     * Retrieves all categories in a hierarchical tree structure.
     *
     * @return array List of categories with nested children.
     */
    public function getAllCategories(): array;

    /**
     * Retrieves details of a single category by its ID.
     *
     * @param int $id The ID of the category.
     *
     * @return array Category data as an associative array.
     */
    public function getCategoryById(int $id): array;

    /**
     * Validates and saves a category.
     * Creates a new one or updates an existing one.
     *
     * @param CategoryModel $category The category to save.
     *
     * @return CategoryModel The saved category with updated fields (e.g., ID).
     */
    public function saveCategory(CategoryModel $category): CategoryModel;

    /**
     * Deletes a category by ID if it contains no products or subcategories with products.
     *
     * @param int $id The ID of the category to delete.
     *
     * @return bool True if deletion was successful, false otherwise.
     */
    public function deleteCategory(int $id): bool;

    /**
     * Retrieves a flat list of all categories (no hierarchy).
     *
     * @return array List of all categories.
     */
    public function getFlatCategories(): array;

}