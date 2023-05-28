<?php

namespace Endroid\Pokemon\Model;

final readonly class Spawn
{
    public int $attack;
    public int $defense;
    public int $stamina;

    public function __construct(
        public Pokemon $pokemon,
        public Level $level,
        public Iv $ivAttack,
        public Iv $ivDefense,
        public Iv $ivStamina
    ) {
        $this->attack = $this->pokemon->baseAttack + $ivAttack->value;
        $this->defense = $this->pokemon->baseDefense + $ivDefense->value;
        $this->stamina = $this->pokemon->baseStamina + $ivStamina->value;
    }

    public function calculateCp(): int
    {
        $scalarFactor = pow($this->level->getCpScalar(), 2);
        $attackFactor = $this->attack;
        $defenseFactor = pow($this->defense, 0.5);
        $staminaFactor = pow($this->stamina, 0.5);

        return (int) floor($attackFactor * $defenseFactor * $staminaFactor * $scalarFactor / 10);
    }

    public function calculateProductAbsolute(): int
    {
        $scalarFactor = $this->level->getCpScalar();
        $attackFactor = $this->attack * $scalarFactor;
        $defenseFactor = $this->defense * $scalarFactor;
        $staminaFactor = $this->stamina * $scalarFactor;

        dump($attackFactor);
        dump($defenseFactor);
        dump($staminaFactor);

        return $attackFactor * $defenseFactor * $staminaFactor;
    }
}
