<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class Pokemon
{
    public function __construct(
        public int $number,
        public string $name,
        public BaseStats $baseStats
    ) {
    }
}
