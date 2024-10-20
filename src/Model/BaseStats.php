<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class BaseStats
{
    public function __construct(
        public int $attack,
        public int $defense,
        public int $stamina,
    ) {
    }

    public function equals(BaseStats $baseStats): bool
    {
        return $this->attack === $baseStats->attack
            && $this->defense === $baseStats->defense
            && $this->stamina === $baseStats->stamina;
    }
}
