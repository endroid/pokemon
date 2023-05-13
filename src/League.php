<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

final class League
{
    public function __construct(
        public readonly string $name,
        public readonly int $maxCp
    ) {
    }
}
