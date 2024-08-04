<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LineAccountStatus extends Enum
{
    const TEMPORARY = 0;
    const CONNECTED = 1;
    const DISCONNECTED = 2;
}
