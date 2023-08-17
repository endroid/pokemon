<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class Spawn
{
    public Stats $stats;

    public function __construct(
        public Pokemon $pokemon,
        public Level $level,
        public Ivs $ivs
    ) {
        $this->stats = new Stats($this->pokemon->baseStats, $this->ivs);
    }

    public function getCp(): int
    {
        $scalarFactor = pow($this->level->getCpScalar(), 2);
        $attackFactor = $this->stats->attack;
        $defenseFactor = pow($this->stats->defense, 0.5);
        $staminaFactor = pow($this->stats->stamina, 0.5);

        return (int) floor($attackFactor * $defenseFactor * $staminaFactor * $scalarFactor / 10);
    }

    public function getStatProduct(): float
    {
        $scalarFactor = $this->level->getCpScalar();
        $attackFactor = $this->stats->attack * $scalarFactor;
        $defenseFactor = $this->stats->defense * $scalarFactor;
        $staminaFactor = floor($this->stats->stamina * $scalarFactor);

        return $attackFactor * $defenseFactor * $staminaFactor;
    }
}
