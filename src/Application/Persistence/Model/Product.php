<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Model;
use DemoShop\Application\Persistence\Model\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'title',
        'sku',
        'brand',
        'category_id',
        'short_description',
        'price',
        'enabled',
        'featured',
        'image_path'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
