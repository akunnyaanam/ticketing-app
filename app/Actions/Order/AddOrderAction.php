<?php

namespace App\Actions\Order;

use App\Actions\Traits\HasUser;
use App\Enums\RoleEnum;
use App\Models\Event;
use Lorisleiva\Actions\Concerns\AsAction;

class AddOrderAction
{
    use AsAction, HasUser;

    public function handle(Event $event): void
    {
        $user = $this->resolveUserRole(RoleEnum::USER);

        if (! $event->isAvailable()) {
            throw new \RuntimeException('The event is not available for ordering tickets.');
        }

        $order = $user->getBookingForEvent($event);

        if (! $order) {
            throw new \RuntimeException('No existing order found for this event.');
        }

        // ::make()->handle($orderDetail);
    }
}
