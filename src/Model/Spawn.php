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

    public function calculateCp(): int
    {
        $scalarFactor = pow($this->level->getCpScalar(), 2);
        $attackFactor = $this->stats->attack;
        $defenseFactor = pow($this->stats->defense, 0.5);
        $staminaFactor = pow($this->stats->stamina, 0.5);

        return (int) floor($attackFactor * $defenseFactor * $staminaFactor * $scalarFactor / 10);
    }

    public function calculateProductAbsolute(): int
    {
        $scalarFactor = $this->level->getCpScalar();
        $attackFactor = $this->stats->attack * $scalarFactor;
        $defenseFactor = $this->stats->defense * $scalarFactor;
        $staminaFactor = $this->stats->stamina * $scalarFactor;

        dump($attackFactor);
        dump($defenseFactor);
        dump($staminaFactor);

        return $attackFactor * $defenseFactor * $staminaFactor;
    }
}
