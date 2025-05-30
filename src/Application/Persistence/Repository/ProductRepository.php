<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\BusinessLogic\RepositoryInterface\ProductRepositoryInterface;
use DemoShop\Application\Persistence\Model\Product;

class ProductRepository implements ProductRepositoryInterface
{

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

    private function buildCategoryPath($product): string
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
}