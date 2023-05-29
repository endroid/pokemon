<?php

namespace Endroid\Pokemon\Model;

final readonly class BaseStats
{
    public function __construct(
        public int $attack,
        public int $defense,
        public int $stamina
    ) {
    }
}
