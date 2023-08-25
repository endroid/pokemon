<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final class Pokemon
{
    public function __construct(
        public readonly int $number,
        public readonly string $name,
        public readonly string $form,
        public readonly BaseStats $baseStats,
        public array $leagueInfo = []
    ) {
    }

    public function createSpawn(Level $level, Ivs $ivs): Spawn
    {
        return new Spawn($this, $level, $ivs);
    }

    public function getPerfectRaidBoss(bool $weatherBoosted): Spawn
    {
        return $this->createSpawn(Level::raidBoss($weatherBoosted), Ivs::max());
    }

    public function getPerfectSpawnForLeague(League $league): Spawn
    {
        $bestSpawn = null;
        $bestStatProduct = 0;
        foreach (Level::all() as $level) {
            foreach (Ivs::all() as $ivs) {
                $spawn = new Spawn($this, $level, $ivs);
                $cp = $spawn->getCp();
                if ($cp > $league->getMaxCp()) {
                    continue;
                }
                $statProduct = $spawn->getStatProduct();
                if ($statProduct >= $bestStatProduct) {
                    $bestSpawn = $spawn;
                    $bestStatProduct = $statProduct;
                }
            }
        }

        return $bestSpawn;
    }
}
