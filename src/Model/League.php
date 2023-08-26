<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

enum League: string
{
    case Great = 'GL';
    case Ultra = 'UL';
    case Master = 'ML';

    public function getMaxCp(): int
    {
        return match ($this) {
            self::Great => 1500,
            self::Ultra => 2500,
            self::Master => 10000,
        };
    }
}
