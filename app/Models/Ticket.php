<?php

namespace App\Models;

use App\Concerns\DefaultGuarded;
use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory, SoftDeletes, DefaultGuarded;

    protected $casts = [
        'price' => 'decimal:2',
        'type' => TicketType::class,
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
