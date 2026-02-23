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
    ) {}

    public function findAll(): PokemonCollection
    {
        $releasedPokemon = $this->poGoApiClient->getReleasedPokemon();
        $pokemonStats = $this->poGoApiClient->getPokemonStats();
        /** @var list<array{pokemon_id: int, type: array<string>}> $pokemonTypesData */
        $pokemonTypesData = $this->poGoApiClient->getPokemonTypes();
        /** @var array<int, array<string>> $pokemonTypes */
        $pokemonTypes = array_column($pokemonTypesData, 'type', 'pokemon_id');

        $pokemonCollection = new PokemonCollection();
        /** @var array{pokemon_id: int, pokemon_name: string, form: string, base_attack: int, base_defense: int, base_stamina: int} $pokemonData */
        foreach ($pokemonStats as $pokemonData) {
            if (!array_key_exists($pokemonData['pokemon_id'], $releasedPokemon)) {
                continue;
            }
            $pokemonCollection->add(
                new Pokemon(
                    $pokemonData['pokemon_id'],
                    $pokemonData['pokemon_name'],
                    str_replace(['_', 'Alola'], [' ', 'Alolan'], $pokemonData['form']),
                    array_map(static fn(string $type) => Type::from(strtolower(
                        $type,
                    )), $pokemonTypes[$pokemonData['pokemon_id']]),
                    new BaseStats(
                        $pokemonData['base_attack'],
                        $pokemonData['base_defense'],
                        $pokemonData['base_stamina'],
                    ),
                ),
            );
        }

        $pokemonCollection->removeDuplicates();

        foreach (League::cases() as $league) {
            $rankings = $this->pvPokeClient->getRankingsForLeague($league, 100);
            /** @var array{speciesName: string, score: float} $ranking */
            foreach (array_values($rankings) as $index => $ranking) {
                $nameParts = explode(' (', $ranking['speciesName']);
                $name = $nameParts[0];
                $form = array_key_exists(1, $nameParts) ? trim($nameParts[1], characters: ')') : 'normal';
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
                    continue;
                }
            }
        }

        return $pokemonCollection;
    }
}
