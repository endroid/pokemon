<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Client;

use Endroid\Asset\Factory\AssetFactory;

final class PoGoApiClient
{
    public function __construct(
        private AssetFactory $assetFactory
    ) {
    }

    public function getPokemonStats(): array
    {
        $cacheAsset = $this->assetFactory->create(null, [
            'url' => 'https://pogoapi.net/api/v1/pokemon_stats.json',
            'cache_key' => 'pokemon-stats',
            'cache_expires_after' => 3600,
        ]);

        $pokemonStats = json_decode($cacheAsset->getData(), true);

        if (!is_array($pokemonStats) || !isset($pokemonStats[0]['base_attack'])) {
            throw new \RuntimeException('Could not fetch Pokemon stats');
        }

        return $pokemonStats;
    }
}
