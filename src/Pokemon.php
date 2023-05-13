<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

final class Pokemon
{
    public function __construct(
        public readonly string $name
    ) {
    }
}
