<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

final readonly class League
{
    public function __construct(
        public string $name,
        public int $maxCp
    ) {
    }
}
