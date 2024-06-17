<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum RoleEnum: int
{
    use EnumToArray;

    case SUPER_ADMIN = 1;
    case IT_SUPPORT = 2;
    case STAFF = 3;

    public function label(): string
    {
        return match (true) {
            strpos($this->name, '_') !== false => str(str_replace('_', ' ', $this->name))->title(),
            default => str($this->name)->title(),
        };
    }
}
