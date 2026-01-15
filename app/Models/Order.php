<?php

namespace App\Models;

use App\Concerns\DefaultGuarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperOrder
 */
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes, DefaultGuarded;

    protected $attributes = [
        'total_price' => 0,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
