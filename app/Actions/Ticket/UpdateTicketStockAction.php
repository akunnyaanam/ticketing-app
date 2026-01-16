<?php

namespace App\Actions\Ticket;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateTicketStockAction
{
    use AsAction;

    public function handle(Ticket $ticket, int $adjustment): void
    {
        DB::transaction(function () use ($ticket, $adjustment) {
            $ticket = Ticket::where('id', $ticket->id)->lockForUpdate()->first();

            $newStock = $ticket->stock + $adjustment;

            if ($newStock < 0) {
                throw new \RuntimeException("['{$ticket->id}'] Ticket stock is less than zero.");
            }

            $ticket->update(['stock' => $newStock]);
        });
    }

    public function increment(Ticket $ticket, int $amount): void
    {
        $this->handle($ticket, $amount);
    }

    public function decrement(Ticket $ticket, int $amount): void
    {
        $this->handle($ticket, -$amount);
    }
}
