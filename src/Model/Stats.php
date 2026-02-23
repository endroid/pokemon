<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final class Stats
{
    public int $attack {
        get => $this->baseStats->attack + $this->ivs->attack->value;
    }
    public int $defense {
        get => $this->baseStats->defense + $this->ivs->defense->value;
    }
    public int $stamina {
        get => $this->baseStats->stamina + $this->ivs->stamina->value;
    }

    public function __construct(
        public readonly BaseStats $baseStats,
        public readonly Ivs $ivs,
    ) {}
}
