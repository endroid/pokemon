<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

use Endroid\Pokemon\Client\PoGoApiClient;
use Endroid\Pokemon\Client\PvPokeClient;
use Endroid\Pokemon\Model\BaseStats;
use Endroid\Pokemon\Model\League;
use Endroid\Pokemon\Model\Pokemon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class PokemonRepository
{
    public function __construct(
        private PoGoApiClient $poGoApiClient,
        private PvPokeClient $pvPokeClient
    ) {
    }

    public function findAll(): PokemonCollection
    {
        $releasedPokemon = $this->poGoApiClient->getReleasedPokemon();
        $pokemonStats = $this->poGoApiClient->getPokemonStats();

        $pokemonCollection = new PokemonCollection();
        foreach ($pokemonStats as $pokemonData) {
            if (!isset($releasedPokemon[$pokemonData['pokemon_id']])) {
                continue;
            }
            $pokemonCollection->add(new Pokemon(
                $pokemonData['pokemon_id'],
                $pokemonData['pokemon_name'],
                str_replace('Alola', 'Alolan', $pokemonData['form']),
                new BaseStats(
                    $pokemonData['base_attack'],
                    $pokemonData['base_defense'],
                    $pokemonData['base_stamina']
                ),
            ));
        }

        foreach (League::cases() as $league) {
            $rankings = $this->pvPokeClient->getRankingsForLeague($league);
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
                        'moves' => $ranking['moveset'],
                    ];
                } catch (NotFoundHttpException) {
                    // Skip the item
                }
            }

            //            $trainingAnalysis = $this->pvPokeClient->getTrainingAnalysisForLeague($league);
            //            foreach ($trainingAnalysis['performers'] as $performer) {
            //                $name = explode(' ', $performer['pokemon'])[0];
            //                $pokemon = $pokemonCollection->findByName($name);
            //                dump($trainingAnalysis['performers']);
            //                die;
            //            }
            //            foreach ($trainingAnalysis['teams'] as $team) {
            //                $pokemon = explode('|', $team['team']);
            //                foreach ($pokemon as $pokemonName) {
            //                    $name = explode(' ', $pokemonName)[0];
            //                    $name = explode('_', $name)[0];
            //                    dump($name);
            //                    $pokemon = $pokemonCollection->findByName($name);
            // //
            //                }
            //            }
        }

        return $pokemonCollection;
    }
}
