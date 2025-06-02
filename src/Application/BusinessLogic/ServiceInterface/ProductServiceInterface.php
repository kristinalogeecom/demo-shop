<?php

namespace DemoShop\Application\BusinessLogic\ServiceInterface;

use DemoShop\Application\BusinessLogic\DTO\Product;

/**
 * Interface for product-related business logic operations.
 */
interface ProductServiceInterface
{
    /**
     * Retrieves all products from the database.
     *
     * @return array An array of products as associative arrays or DTOs.
     */
    public function getAllProducts(): array;


    /**
     * Retrieves a paginated list of products.
     *
     * @param int $page Current page number (1-based index).
     *
     * @return array An array containing product data and pagination metadata.
     */
    public function getPaginatedProducts(int $page): array;

    /**
     * Validates and saves the given product.
     *
     * @param Product $product Product DTO containing product data.
     *
     * @return Product|null The saved product DTO with updated fields, or null on failure.
     */
    public function saveProduct(Product $product): ?Product;

    /**
     * Deletes a product by its ID.
     *
     * @param int $id The ID of the product to delete.
     *
     * @return bool True if deletion was successful, false otherwise.
     */
    public function deleteProductById(int $id): bool;


    /**
     * Deletes multiple products by their IDs.
     *
     * @param array $ids An array of product IDs to delete.
     *
     * @return int The number of successfully deleted products.
     */
    public function deleteMultipleProducts(array $ids): int;

    /**
     * Updates the "enabled" status of multiple products.
     *
     * @param array $ids An array of product IDs.
     * @param bool $enabled New status value (true = enabled, false = disabled).
     *
     * @return void
     */
    public function updateEnabledStatus(array $ids, bool $enabled): void;

    /**
     * Handles the upload of a product image and returns the relative path.
     *
     * @param array $image The uploaded image file from $_FILES.
     *
     * @return string Relative path to the saved image file.
     */
    public function handleImageUpload(array $image): string;
}