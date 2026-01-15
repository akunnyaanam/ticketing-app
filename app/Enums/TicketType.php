<?php

namespace App\Enums;

use App\Concerns\EnumUtils;

enum TicketType: string
{
    use EnumUtils;

    case REGULAR = 'REG';
    case PREMIUM = 'PRE';
}
