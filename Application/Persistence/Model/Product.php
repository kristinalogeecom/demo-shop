<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a product stored in the 'products' table.
 */
class Product extends Model
{
    protected $table = 'products';
}