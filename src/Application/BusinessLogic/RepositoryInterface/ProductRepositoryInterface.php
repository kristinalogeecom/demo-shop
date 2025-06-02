<?php

namespace DemoShop\Application\BusinessLogic\RepositoryInterface;

use DemoShop\Application\BusinessLogic\DTO\Product;

/**
 * Interface for product data access layer.
 *
 * Defines methods for retrieving, saving, updating and deleting product records.
 */
interface ProductRepositoryInterface
{
    /**
     * Retrieves all products with their parent category path.
     *
     * @return array
     */
    public function getAllProducts(): array;

    /**
     * Retrieves a paginated list of products.
     *
     * @param int $page     Current page number (default is 1).
     * @param int $perPage  Number of products per page (default is 10).
     *
     * @return array An array with keys: products, total, perPage, currentPage, lastPage.
     */
    public function getPaginatedProducts(int $page = 1, int $perPage = 10): array;

    /**
     * Saves a new product or updates an existing one based on the given DTO.
     *
     * @param Product $dto Data Transfer Object containing product data.
     *
     * @return Product|null The saved product DTO, or null if saving failed.
     */
    public function saveProduct(Product $dto): ?Product;

    /**
     * Deletes a product by its ID.
     *
     * @param int $id The ID of the product to delete.
     *
     * @return True if the product was deleted, false otherwise.
     */
    public function deleteProductById(int $id): bool;

    /**
     * Deletes multiple products by their IDs.
     *
     * @param array $ids Array of product IDs to delete.
     *
     * @return int Number of successfully deleted products.
     */
    public function deleteMultipleProducts(array $ids): int;


    /**
     * Updates the 'enabled' status for multiple products.
     *
     * @param array $ids     Array of product IDs.
     * @param bool  $enabled New enabled status (true or false).
     *
     * @return void
     */
    public function updateEnabledStatus(array $ids, bool $enabled): void;
}