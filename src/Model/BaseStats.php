<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final class BaseStats
{
    public function __construct(
        public readonly int $attack,
        public readonly int $defense,
        public readonly int $stamina
    ) {
    }

    public function equals(BaseStats $baseStats): bool
    {
        return $this->attack === $baseStats->attack
            && $this->defense === $baseStats->defense
            && $this->stamina === $baseStats->stamina;
    }
}
