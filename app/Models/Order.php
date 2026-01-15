<?php

namespace App\Models;

use App\Concerns\DefaultGuarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperOrder
 */
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use DefaultGuarded, HasFactory, SoftDeletes;

    protected $attributes = [
        'total_price' => 0,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
