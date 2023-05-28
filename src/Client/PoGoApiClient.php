<?php

namespace Endroid\Pokemon\Client;

final class PoGoApiClient
{
    public function getPokemonStats(): array
    {
        $pokemonStats = json_decode((string) file_get_contents('https://pogoapi.net/api/v1/pokemon_stats.json'), true);

        if (!is_array($pokemonStats) || !isset($pokemonStats[0]['base_attack'])) {
            throw new \RuntimeException('Could not fetch Pokemon stats');
        }

        return $pokemonStats;
    }
}
