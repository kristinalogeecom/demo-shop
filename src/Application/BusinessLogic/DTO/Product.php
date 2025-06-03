<?php

namespace DemoShop\Application\BusinessLogic\DTO;

/**
 * @method static where(string $string, mixed $sku)
 */
class Product
{
    public ?int $id;
    public string $sku;

    public string $title;
    public string $brand;
    public int $categoryId;
    public float $price;
    public string $shortDescription;
    public string $longDescription;

    public bool $enabled;
    public bool $featured;
    public ?string $imagePath;

    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->sku = $data['sku'];
        $this->title = $data['title'];
        $this->brand = $data['brand'];
        $this->categoryId = (int)($data['category_id'] ?? $data['category'] ?? 0);
        $this->price = (float)$data['price'];
        $this->shortDescription = $data['short_description'];
        $this->longDescription = $data['long_description'] ?? '';
        $this->enabled = !empty($data['enabled']);
        $this->featured = !empty($data['featured']);
        $this->imagePath = $data['image_path'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }



}
