<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Model;
use DemoShop\Application\Persistence\Model\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static find(mixed $id)
 * @method static whereIn(string $string, array $ids)
 * @method static where(string $string, mixed $sku)
 */
class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'title',
        'sku',
        'brand',
        'category_id',
        'short_description',
        'long_description',
        'price',
        'enabled',
        'featured',
        'image_path'
    ];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
