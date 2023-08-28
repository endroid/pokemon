<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final class Stats
{
    public readonly int $attack;
    public readonly int $defense;
    public readonly int $stamina;

    public function __construct(
        public BaseStats $baseStats,
        public Ivs $ivs,
    ) {
        $this->attack = $this->baseStats->attack + $this->ivs->attack->value;
        $this->defense = $this->baseStats->defense + $this->ivs->defense->value;
        $this->stamina = $this->baseStats->stamina + $this->ivs->stamina->value;
    }
}
