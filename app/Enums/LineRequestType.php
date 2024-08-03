<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class LineRequestType extends Enum
{
    const FOLLOW = 'follow';
    const UNFOLLOW = 'unfollow';
}
