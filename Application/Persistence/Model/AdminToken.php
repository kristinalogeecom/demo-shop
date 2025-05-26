<?php

namespace DemoShop\Application\Persistence\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, string $token)
 */
class AdminToken extends Model
{
    protected $table = 'admin_tokens';

    protected $fillable = [
        'admin_id',
        'token',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

}
