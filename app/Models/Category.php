<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCategory
 */
class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
