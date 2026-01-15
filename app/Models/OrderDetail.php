<?php

namespace App\Models;

use App\Concerns\DefaultGuarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperOrderDetail
 */
class OrderDetail extends Model
{
    /** @use HasFactory<\Database\Factories\OrderDetailFactory> */
    use DefaultGuarded, HasFactory, SoftDeletes;

    protected $attributes = [
        'sub_total' => 0,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
