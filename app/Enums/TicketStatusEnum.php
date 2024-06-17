<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TicketStatusEnum: string
{
    use EnumToArray;

    case WAITING = 'Dalam Antrian';
    case RESPONSE = 'Menunggu Diproses';
    case PROCESS = 'Sedang Diproses';
    case SOLVED = 'Terselesaikan';
}
