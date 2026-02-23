<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class Ivs implements \Stringable
{
    public function __construct(
        public Iv $attack,
        public Iv $defense,
        public Iv $stamina,
    ) {}

    /** @return \Generator<int, Ivs> */
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

    #[\Override]
    public function __toString(): string
    {
        return $this->attack->value . '-' . $this->defense->value . '-' . $this->stamina->value;
    }
}
