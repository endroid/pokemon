<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class Pokemon
{
    public function __construct(
        public int $number,
        public string $name,
        public int $baseAttack,
        public int $baseDefense,
        public int $baseStamina
    ) {
    }

    public function createSpawn(Level $level, Iv $attack, Iv $defense, Iv $stamina): Spawn
    {
        return new Spawn($this, $level, $attack, $defense, $stamina);
    }
}
