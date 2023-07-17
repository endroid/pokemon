<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

use Endroid\Pokemon\Client\PoGoApiClient;
use Endroid\Pokemon\Client\PvPokeClient;
use Endroid\Pokemon\Model\BaseStats;
use Endroid\Pokemon\Model\Ivs;
use Endroid\Pokemon\Model\League;
use Endroid\Pokemon\Model\Level;
use Endroid\Pokemon\Model\Pokemon;
use Endroid\Pokemon\Model\Spawn;

final readonly class PokemonRepository
{
    public function __construct(
        private PoGoApiClient $poGoApiClient,
        private PvPokeClient $pvPokeClient
    ) {
    }

    public function findAll(): PokemonCollection
    {
        $pokemonStats = $this->poGoApiClient->getPokemonStats();

        $pokemonCollection = new PokemonCollection();
        foreach ($pokemonStats as $pokemonData) {
            $pokemonCollection->add(new Pokemon(
                $pokemonData['pokemon_id'],
                $pokemonData['pokemon_name'],
                new BaseStats(
                    $pokemonData['base_attack'],
                    $pokemonData['base_defense'],
                    $pokemonData['base_stamina']
                ),
            ));
        }

        foreach (League::cases() as $league) {
            $trainingAnalysis = $this->pvPokeClient->getTrainingAnalysisForLeague($league);
            foreach ($trainingAnalysis['performers'] as $performer) {
                $name = explode(' ', $performer['pokemon'])[0];
                $name = explode('_', $name)[0];
                $pokemon = $pokemonCollection->findByName($name);
                $spawn = $this->getBestSpawnForLeague($pokemon, $league);
            }
            foreach ($trainingAnalysis['teams'] as $team) {
                $pokemon = explode('|', $team['team']);
                foreach ($pokemon as $pokemonName) {
                    $name = explode(' ', $pokemonName)[0];
                    $name = explode('_', $name)[0];
                    $pokemon = $pokemonCollection->findByName($name);
                    $spawn = $this->getBestSpawnForLeague($pokemon, $league);
                }
            }
        }

        return $pokemonCollection;
    }

    private function getBestSpawnForLeague(Pokemon $pokemon, League $league): Spawn
    {
        $maxCp = 0;
        $maxSpawn = null;
        foreach (Level::all() as $level) {
            foreach (Ivs::all() as $ivs) {
                $spawn = new Spawn($pokemon, $level, $ivs);
                $cp = $spawn->calculateCp();
                if ($cp > $maxCp && $cp <= $league->getMaxCp()) {
                    $maxCp = $cp;
                    $maxSpawn = $spawn;
                }
            }
        }

        dump($maxSpawn);
        exit;

        return $maxSpawn;
    }
}
