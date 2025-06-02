<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\DTO\Product as ProductDTO;
use DemoShop\Application\Persistence\Model\Product;
use DemoShop\Application\BusinessLogic\RepositoryInterface\ProductRepositoryInterface;
use DemoShop\Application\BusinessLogic\ServiceInterface\ProductServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\ImageException;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Exception\ValidationException;


/**
 * Service layer for managing product-related business logic.
 */
class ProductService implements ProductServiceInterface
{
    private ProductRepositoryInterface $productRepository;

    /**
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        $this->productRepository = ServiceRegistry::get(ProductRepositoryInterface::class);
    }

    /**
     * Retrieves all products.
     *
     * @return array
     */
    public function getAllProducts(): array
    {
        return $this->productRepository->getAllProducts();
    }

    /**
     * Retrieves paginated product list.
     *
     * @param int $page
     *
     * @return array
     */
    public function getPaginatedProducts(int $page): array
    {
        return $this->productRepository->getPaginatedProducts($page);
    }

    /**
     * Validates and saves the product.
     *
     * @param ProductDTO $product
     *
     * @return ProductDTO|null The saved product DTO or null on failure.
     *
     * @throws ValidationException
     */
    public function saveProduct(ProductDTO $product): ?ProductDTO
    {
        $this->validateProduct($product);
        return $this->productRepository->saveProduct($product);
    }

    /**
     * Deletes a product by ID.
     *
     * @param int $id
     *
     * @return bool True if deleted successfully, false otherwise.
     */
    public function deleteProductById(int $id): bool
    {
        return $this->productRepository->deleteProductById($id);
    }

    /**
     * Deletes multiple products.
     *
     * @param array $ids
     *
     * @return int Number of products successfully deleted.
     */
    public function deleteMultipleProducts(array $ids): int
    {
        return $this->productRepository->deleteMultipleProducts($ids);
    }

    /**
     * Updates enabled status for a group of products.
     *
     * @param array $ids
     * @param bool $enabled
     *
     * @return void
     */
    public function updateEnabledStatus(array $ids, bool $enabled): void
    {
        $this->productRepository->updateEnabledStatus($ids, $enabled);
    }


    /**
     * Handles image upload and returns relative path.
     *
     * @param array $image Uploaded file data ($_FILES['image']).
     *
     * @return string Relative path to saved image.
     *
     * @throws ImageException
     */
    public function handleImageUpload(array $image): string
    {
        if (!str_starts_with($image['type'], 'image/')) {
            throw new ImageException('Only image uploads are allowed.');
        }

        $targetDir = __DIR__ . '/../../../../resources/images';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $filename = uniqid('product', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        $destination = $targetDir . '/' . $filename;

        if (!move_uploaded_file($image['tmp_name'], $destination)) {
            throw new ImageException('Failed to save uploaded image.');
        }

        return 'resources/images/' . $filename;
    }

    /**
     * Validates product data before save.
     *
     * @param ProductDTO $product
     *
     * @return void
     *
     * @throws ValidationException
     */
    private function validateProduct(ProductDTO $product): void
    {
        if (trim($product->title) === '') {
            throw new ValidationException('Title is required.');
        }

        if (trim($product->sku) === '') {
            throw new ValidationException('SKU is required.');
        }

        $duplicateSku = Product::where('sku', $product->sku)
            ->when($product->id !== null, fn($query) => $query->where('id', '!=', $product->id))
            ->exists();

        if ($duplicateSku) {
            throw new ValidationException('SKU must be unique.');
        }

        if (trim($product->brand) === '') {
            throw new ValidationException('Brand is required.');
        }

        if (!is_numeric($product->price) || $product->price <= 0) {
            throw new ValidationException('Price must be a positive number.');
        }

        if (trim($product->shortDescription) === '') {
            throw new ValidationException('Short description is required.');
        }

        if (empty($product->categoryId)) {
            throw new ValidationException('Category must be selected.');
        }
    }
}