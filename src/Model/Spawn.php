<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class Spawn
{
    public Stats $stats;

    public function __construct(
        public Pokemon $pokemon,
        public Level $level,
        public Ivs $ivs,
    ) {
        $this->stats = new Stats($this->pokemon->baseStats, $this->ivs);
    }

    public function getCp(): int
    {
        $cpMultiplier = pow($this->level->getCpMultiplier(), 2);
        $attackFactor = $this->stats->attack;
        $defenseFactor = pow($this->stats->defense, 0.5);
        $staminaFactor = pow($this->stats->stamina, 0.5);

        return (int) floor($attackFactor * $defenseFactor * $staminaFactor * $cpMultiplier / 10);
    }

    public function getStatProduct(): float
    {
        $cpMultiplier = $this->level->getCpMultiplier();
        $attackFactor = $this->stats->attack * $cpMultiplier;
        $defenseFactor = $this->stats->defense * $cpMultiplier;
        $staminaFactor = floor($this->stats->stamina * $cpMultiplier);

        return $attackFactor * $defenseFactor * $staminaFactor;
    }
}
