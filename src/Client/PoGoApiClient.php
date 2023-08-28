<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Client;

use Endroid\Asset\Factory\AssetFactory;

final class PoGoApiClient
{
    public function __construct(
        private readonly AssetFactory $assetFactory
    ) {
    }

    /** @return array<mixed> */
    public function getPokemonStats(): array
    {
        return $this->doRequest('pokemon_stats');
    }

    /** @return array<mixed> */
    public function getReleasedPokemon(): array
    {
        return $this->doRequest('released_pokemon');
    }

    /** @return array<mixed> */
    public function getPokemonTypes(): array
    {
        return $this->doRequest('pokemon_types');
    }

    /** @return array<mixed> */
    public function getMegas(): array
    {
        return $this->doRequest('mega_pokemon');
    }

    /** @return array<mixed> */
    public function doRequest(string $path): array
    {
        $cacheAsset = $this->assetFactory->create(null, [
            'url' => 'https://pogoapi.net/api/v1/'.$path.'.json',
            'cache_key' => 'pokemon-'.str_replace('_', '-', $path),
            'cache_expires_after' => 86400,
        ]);

        $responseData = json_decode($cacheAsset->getData(), true);

        if (!is_array($responseData)) {
            throw new \RuntimeException(sprintf('Could not fetch %s data', $path));
        }

        return $responseData;
    }
}
