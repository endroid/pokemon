<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final class Ivs implements \Stringable
{
    public function __construct(
        public readonly Iv $attack,
        public readonly Iv $defense,
        public readonly Iv $stamina
    ) {
    }

    public static function all(): \Generator
    {
        foreach (Iv::all() as $attack) {
            foreach (Iv::all() as $defense) {
                foreach (Iv::all() as $stamina) {
                    yield new self($attack, $defense, $stamina);
                }
            }
        }
    }

    public static function max(): self
    {
        return new self(Iv::max(), Iv::max(), Iv::max());
    }

    public function __toString(): string
    {
        return $this->attack->value.'-'.$this->defense->value.'-'.$this->stamina->value;
    }
}
