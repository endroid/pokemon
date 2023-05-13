<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

final class PokemonRepository
{
    /** @return array<Pokemon>  */
    public function findBestForLeague(League $league): array
    {
        $best = [];

        /** @var array<mixed> $data */
        $data = json_decode((string) file_get_contents('https://pvpoke.com/data/training/analysis/all/'.$league->maxCp.'.json'), true);

        foreach ($data['performers'] as $performer) {
            $name = explode(' ', $performer['pokemon'])[0];
            $best[$name] = new Pokemon($name);
        }

        foreach ($data['teams'] as $team) {
            $pokemon = explode('|', $team['team']);
            foreach ($pokemon as $pokemonName) {
                $name = explode(' ', $pokemonName)[0];
                $best[$name] = new Pokemon($name);
            }
        }

        ksort($best);

        return $best;
    }
}
