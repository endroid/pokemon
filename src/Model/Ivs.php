<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class Ivs
{
    public function __construct(
        public Iv $attack,
        public Iv $defense,
        public Iv $stamina
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
}
