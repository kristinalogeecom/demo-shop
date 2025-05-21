<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

/**
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