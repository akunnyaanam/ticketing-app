<?php

namespace App\Enums;

use App\Concerns\EnumUtils;

enum RoleEnum: string
{
    use EnumUtils;

    case ADMIN = 'admin';
    case USER = 'user';
}
