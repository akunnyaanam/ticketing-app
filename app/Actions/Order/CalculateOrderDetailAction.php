<?php

namespace App\Actions\Order;

use App\Models\OrderDetail;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateOrderDetailAction
{
    use AsAction;

    public function handle(OrderDetail $orderDetail): void
    {
        $price = $orderDetail->ticket->price;
        $quantity = $orderDetail->quantity;

        $orderDetail->update([
            'sub_total' => $price * $quantity,
        ]);

        CalculateOrderTotalAction::make()->handle($orderDetail->order);
    }
}
