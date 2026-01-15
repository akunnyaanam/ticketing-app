<?php

namespace App\Models;

use App\Concerns\DefaultGuarded;
use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperTicket
 */
class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use DefaultGuarded, HasFactory, SoftDeletes;

    protected $casts = [
        'price' => 'decimal:2',
        'type' => TicketType::class,
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function isTicketAvailable(): bool
    {
        return $this->available_quantity > 0;
    }
}
