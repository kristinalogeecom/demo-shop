<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents an admin user stored in the 'admins' database table.
 *
 * @property string $username
 * @property string $password
 * @property int $id
 *
 * @method static Builder|self where(string $column, mixed $value)
 * @method static Builder|self find(mixed $id)
 * @method static Builder|self create(array $attributes)
 */
class Admin extends Model
{
    public $timestamps = false;
    protected $table = 'admins';
    protected $fillable = ['username', 'password'];
}