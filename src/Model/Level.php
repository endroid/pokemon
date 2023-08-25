<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class Level
{
    public const MIN = 1.0;
    public const MAX = 50.0;
    public const RAID_BOSS = 20.0;
    public const RAID_BOSS_WEATHER_BOOSTED = 25.0;

    public function __construct(
        public float $value,
        public float $cpMultiplier
    ) {
        if ($value < self::MIN || $value > self::MAX) {
            throw new \InvalidArgumentException(sprintf('Invalid level "%s"', $value));
        }
    }
}
