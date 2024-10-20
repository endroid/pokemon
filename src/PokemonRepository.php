<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

use Endroid\Pokemon\Client\PoGoApiClient;
use Endroid\Pokemon\Client\PvPokeClient;
use Endroid\Pokemon\Exception\NotFoundException;
use Endroid\Pokemon\Model\BaseStats;
use Endroid\Pokemon\Model\League;
use Endroid\Pokemon\Model\Pokemon;
use Endroid\Pokemon\Model\Type;

final readonly class PokemonRepository
{
    public function __construct(
        private PoGoApiClient $poGoApiClient,
        private PvPokeClient $pvPokeClient,
    ) {
    }

    public function findAll(): PokemonCollection
    {
        $releasedPokemon = $this->poGoApiClient->getReleasedPokemon();
        $pokemonStats = $this->poGoApiClient->getPokemonStats();
        $pokemonTypes = array_column($this->poGoApiClient->getPokemonTypes(), 'type', 'pokemon_id');

        $pokemonCollection = new PokemonCollection();
        foreach ($pokemonStats as $pokemonData) {
            if (!isset($releasedPokemon[$pokemonData['pokemon_id']])) {
                continue;
            }
            $pokemonCollection->add(new Pokemon(
                $pokemonData['pokemon_id'],
                $pokemonData['pokemon_name'],
                str_replace(['_', 'Alola'], [' ', 'Alolan'], $pokemonData['form']),
                array_map(fn (string $type) => Type::from(strtolower($type)), $pokemonTypes[$pokemonData['pokemon_id']]),
                new BaseStats(
                    $pokemonData['base_attack'],
                    $pokemonData['base_defense'],
                    $pokemonData['base_stamina']
                ),
            ));
        }

        $pokemonCollection->removeDuplicates();

        foreach (League::cases() as $league) {
            $rankings = $this->pvPokeClient->getRankingsForLeague($league, 100);
            foreach ($rankings as $index => $ranking) {
                $nameParts = explode(' (', $ranking['speciesName']);
                $name = $nameParts[0];
                $form = isset($nameParts[1]) ? trim($nameParts[1], ')') : 'normal';
                if ('Shadow' === $form) {
                    continue;
                }
                try {
                    $pokemon = $pokemonCollection->find($name, $form);
                    $pokemon->leagueInfo[$league->name] = [
                        'rank' => $index + 1,
                        'score' => $ranking['score'],
                    ];
                } catch (NotFoundException) {
                    // Skip the item
                }
            }
        }

        return $pokemonCollection;
    }
}
