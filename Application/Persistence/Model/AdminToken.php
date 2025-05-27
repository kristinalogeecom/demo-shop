<?php

namespace DemoShop\Application\Persistence\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents an authentication token associated with an admin.
 *
 * @property int $id
 * @property int $admin_id
 * @property string $token
 * @property Carbon $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
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
