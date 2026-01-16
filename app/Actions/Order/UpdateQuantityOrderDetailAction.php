<?php

namespace App\Actions\Order;

use App\Actions\Ticket\UpdateTicketStockAction;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateQuantityOrderDetailAction
{
    use AsAction;

    public function handle(OrderDetail $orderDetail, int $adjustment): void
    {
        DB::transaction(function () use ($orderDetail, $adjustment) {
            $orderDetail = OrderDetail::where('id', $orderDetail->id)->lockForUpdate()->first();

            $newStock = $orderDetail->quantity + $adjustment;

            if ($newStock < 0) {
                throw new \RuntimeException("['{$orderDetail->id}'] OrderDetail quantity is less than zero.");
            }

            UpdateTicketStockAction::run($orderDetail->ticket, -$adjustment);

            if ($newQty === 0) {
                $orderDetail->delete();
            } else {
                $orderDetail->update(['quantity' => $newQty]);
                CalculateOrderDetailAction::run($orderDetail);
            }
        });
    }

    public function increment(OrderDetail $orderDetail, int $amount): void
    {
        $this->handle($orderDetail, $amount);
    }

    public function decrement(OrderDetail $orderDetail, int $amount): void
    {
        $this->handle($orderDetail, -$amount);
    }
}
