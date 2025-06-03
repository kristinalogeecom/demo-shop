<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\BusinessLogic\RepositoryInterface\ProductRepositoryInterface;
use DemoShop\Application\Persistence\Model\Product;
use DemoShop\Application\BusinessLogic\DTO\Product as ProductDTO;

/**
 * Repository implementation for managing product data using Eloquent ORM.
 * Handles retrieval, creation, updating, and deletion of product records.
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Retrieves all products with their parent category information.
     *
     * @return array An array of associative arrays representing product data.
     */
    public function getAllProducts(): array
    {
        return Product::with('category.parent')->orderByDesc('id')->get()->map(function ($product) {

            return [
                'id' => $product->id,
                'title' => $product->title,
                'sku' => $product->sku,
                'brand' => $product->brand,
                'price' => $product->price,
                'short_description' => $product->short_description,
                'enabled' => $product->enabled,
                'featured' => $product->featured,
                'category' => $this->buildCategoryPath($product),
            ];

        })->toArray();
    }

    /**
     * Saves a new product to the database using data from the provided DTO.
     *
     * @param ProductDTO $dto Data Transfer Object containing product data.
     *
     * @return ProductDTO|null
     */
    public function saveProduct(ProductDTO $dto): ?ProductDTO
    {
        $product = $dto->id ? Product::find($dto->id) : new Product();

        $product->sku = $dto->sku;
        $product->title = $dto->title;
        $product->brand = $dto->brand;
        $product->category_id = $dto->categoryId;
        $product->price = $dto->price;
        $product->short_description = $dto->shortDescription;
        $product->long_description = $dto->longDescription;
        $product->enabled = $dto->enabled;
        $product->featured = $dto->featured;

        if ($dto->imagePath) {
            $product->image_path = $dto->imagePath;
        }

        if ($product->save()) {
            return new ProductDTO([
                'id' => $product->id,
                'sku' => $product->sku,
                'title' => $product->title,
                'brand' => $product->brand,
                'category_id' => $product->category_id,
                'price' => $product->price,
                'short_description' => $product->short_description,
                'long_description' => $product->long_description,
                'enabled' => $product->enabled,
                'featured' => $product->featured,
                'image_path' => $product->image_path,
            ]);
        }

        return null;
    }

    /**
     * Deletes a product by its ID, including its associated image file if it exists.
     *
     * @param int $id The ID of the product to delete.
     *
     * @return bool
     */
    public function deleteProductById(int $id): bool
    {
        $product = Product::find($id);

        if (!$product) {
            return false;
        }

        if ($product->image_path && file_exists(__DIR__ . '/../../../../' . $product->image_path)) {
            unlink(__DIR__ . '/../../../../' . $product->image_path);
        }

        return $product->delete();

    }

    /**
     * Deletes multiple products by their IDs, including their image files if they exist.
     *
     * @param array $ids Array of product IDs to delete.
     *
     * @return int
     */
    public function deleteMultipleProducts(array $ids): int
    {
        $products = Product::whereIn('id', $ids)->get();

        $deletedCount = 0;
        foreach ($products as $product) {
            if ($product->image_path && file_exists(__DIR__ . '/../../../../' . $product->image_path)) {
                unlink(__DIR__ . '/../../../../' . $product->image_path);
            }

            if ($product->delete()) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Updates the 'enabled' status of multiple products.
     *
     * @param array $ids Array of product IDs.
     * @param bool  $enabled New status value (true to enable, false to disable).
     *
     * @return void
     */
    public function updateEnabledStatus(array $ids, bool $enabled): void
    {
        Product::whereIn('id', $ids)->update(['enabled' => $enabled ? 1 : 0]);
    }


    /**
     * Retrieves paginated and filtered products based on provided filter options.
     *
     * @param array $filters Filters for keyword, category, price range.
     * @param int   $page    Page number.
     * @param int   $perPage Number of items per page.
     *
     * @return array Paginated list of products.
     */
    public function getFilteredProducts(array $filters, int $page = 1, int $perPage = 10): array
    {
        $query = Product::with('category.parent')->orderByDesc('id');

        // Keyword search (title, sku, brand, short_description)
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('title', 'LIKE', "%$q%")
                    ->orWhere('sku', 'LIKE', "%$q%")
                    ->orWhere('brand', 'LIKE', "%$q%")
                    ->orWhere('short_description', 'LIKE', "%$q%");
            });
        }

        // Category filter
        if (!empty($filters['category_ids']) && is_array($filters['category_ids'])) {
            $query->whereIn('category_id', $filters['category_ids']);
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', (float)$filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', (float)$filters['max_price']);
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->mapPaginatedProducts($paginator);
    }

    /**
     * Builds the full category path for a product by walking up the parent hierarchy.
     *
     * @param Product $product Product model instance.
     *
     * @return string Category path in the format "Parent > Child".
     */
    private function buildCategoryPath(Product $product): string
    {
        $category = $product->category;
        if (!$category) return '-';

        $names = [$category->name];
        $parent = $category->parent;

        while ($parent) {
            array_unshift($names, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $names);
    }

    /**
     * Maps a paginator instance to a standardized array format.
     *
     * @param $paginator
     *
     * @return array
     */
    private function mapPaginatedProducts($paginator): array
    {
        return [
            'products' => collect($paginator->items())->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'sku' => $product->sku,
                    'brand' => $product->brand,
                    'price' => $product->price,
                    'short_description' => $product->short_description,
                    'enabled' => $product->enabled,
                    'featured' => $product->featured,
                    'category' => $this->buildCategoryPath($product),
                ];
            })->toArray(),
            'total' => $paginator->total(),
            'perPage' => $paginator->perPage(),
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
        ];
    }


}