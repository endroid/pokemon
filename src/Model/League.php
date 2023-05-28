<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

enum League: string
{
    case GREAT = 'Great League';
    case ULTRA = 'Ultra League';
    case MASTER = 'Master League';

    public function getMaxCp(): int
    {
        return match ($this) {
            self::GREAT => 1500,
            self::ULTRA => 2500,
            self::MASTER => 10000,
        };
    }
}
