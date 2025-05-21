<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents an admin user stored in the 'admins' database table.
 *
 * @property string $username
 * @property string $password
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static where(string $column, mixed $value)
 * @method static \Illuminate\Database\Eloquent\Builder|static first()
 */
class Admin extends Model
{
    public $timestamps = false;
    protected $table = 'admins';
    protected $fillable = ['username', 'password'];
}