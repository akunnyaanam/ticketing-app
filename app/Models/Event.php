<?php

namespace App\Models;

use App\Concerns\DefaultGuarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperEvent
 */
class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use DefaultGuarded, HasFactory, SoftDeletes;

    protected $casts = [
        'total_stock' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('total_stock', function ($builder) {
            $builder->withSum('tickets as total_stock', 'stock');
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isEventUpcoming(): bool
    {
        return $this->datetime > now();
    }

    /**
     * @param  Builder<Model>  $query
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('datetime', '>', now())->whereHas('tickets', fn (Builder $query) => $query->where(
            'stock',
            '>',
            0,
        ));
    }

    public function isAvailable(): bool
    {
        return $this->tickets()->where('stock', '>', 0)->exists() && $this->isEventUpcoming();
    }
}
