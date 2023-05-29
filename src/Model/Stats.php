<?php

namespace Endroid\Pokemon\Model;

final readonly class Stats
{
    public int $attack;
    public int $defense;
    public int $stamina;

    public function __construct(
        public BaseStats $baseStats,
        public Ivs $ivs,
    ) {
        $this->attack = $this->baseStats->attack + $this->ivs->attack->value;
        $this->defense = $this->baseStats->defense + $this->ivs->defense->value;
        $this->stamina = $this->baseStats->stamina + $this->ivs->stamina->value;
    }
}
