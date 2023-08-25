<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

use Endroid\Pokemon\Client\PoGoApiClient;

final class LevelFactory
{
    private array $cpMultipliers = [];

    public function __construct(
        private readonly PoGoApiClient $poGoApiClient
    ) { }

    public function create(float $value): Level
    {
        $cpMultiplier = $this->getCpMultiplier($value);

        return new Level($value, )
    }

    public function all(): \Generator
    {
        for ($value = Level::MIN; $value <= self::LEVEL_MAX; ++$value) {
            yield new self($value);
        }
    }

    public function raidBoss(bool $weatherBoosted): Level
    {
        $value = $weatherBoosted ? Level::RAID_BOSS_WEATHER_BOOSTED : Level::RAID_BOSS;

        return self::create($value);
    }

    public function getCpMultiplier(float $value): float
    {
        if (count($this->cpMultipliers) === 0) {
            $this->cpMultipliers = $this->poGoApiClient->getCpMultipliers();
        }

        if (!isset($this->cpMultipliers[$value])) {
            throw new \InvalidArgumentException(sprintf('No CP multiplier found for level "%s"', $value));
        }

        return $this->cpMultipliers[$value];
    }
}
