<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *  Represents a product category stored in the 'categories' table.
 *
 * @property int $id The primary key.
 * @property int|null $parent_id The ID of the parent category (if any).
 * @property string $name The name of the category.
 * @property string|null $code Optional code for the category.
 * @property string|null $description Optional description of the category.
 *
 * @property Category|null $parent The parent category instance.
 * @property Collection|Category[] $children List of child categories.
 * @property Collection|Product[] $products List of products in this category.
 *
 * @method static where(string $string, int $parent_id)
 * @method static find(int $id)
 * @method static select(string $string, string $string1)
 * @method static withCount(string $string)
 * @method static count()
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
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all products in this category
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Check if category has products
     *
     * @return bool
     */
    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    /**
     * Check if category has subcategories
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

}
