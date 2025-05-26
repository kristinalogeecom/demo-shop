<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, mixed $parent_id)
 * @method static find(mixed $id)
 * @method static select(string $string, string $string1)
 */
class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'description'
    ];

    public $timestamps = false;

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all products in this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Check if category has products
     */
    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    /**
     * Check if category has subcategories
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }


}
