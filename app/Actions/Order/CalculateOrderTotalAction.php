<?php

namespace App\Actions\Order;

use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateOrderTotalAction
{
    use AsAction;

    public function handle(Order $order): void
    {
        $total = $order->details->sum('sub_total');

        $order->update(['total_price' => $total]);
    }
}
