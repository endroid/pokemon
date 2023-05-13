<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

final readonly class Pokemon
{
    public function __construct(
        public string $name
    ) {
    }
}
