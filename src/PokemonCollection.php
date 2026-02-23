<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

use Endroid\Pokemon\Exception\NotFoundException;
use Endroid\Pokemon\Model\BaseStats;
use Endroid\Pokemon\Model\Pokemon;

/**
 * @implements \IteratorAggregate<int, Pokemon>
 */
final class PokemonCollection implements \IteratorAggregate
{
    /** @var array<array<Pokemon>> */
    private array $pokemonByNameAndForm = [];

    public function add(Pokemon $pokemon): void
    {
        $this->pokemonByNameAndForm[$this->createNameKey($pokemon->name)][$this->createFormKey($pokemon->form)] =
            $pokemon;
    }

    public function removeDuplicates(): void
    {
        foreach ($this->pokemonByNameAndForm as &$pokemonByForm) {
            $ivs = null;
            uasort($pokemonByForm, static fn(Pokemon $_a, Pokemon $b) => 'Normal' === $b->form ? 1 : -1);
            foreach ($pokemonByForm as $form => $pokemon) {
                if ($ivs instanceof BaseStats && $pokemon->baseStats->equals($ivs)) {
                    unset($pokemonByForm[$form]);
                    continue;
                }

                $ivs = $pokemon->baseStats;
            }
        }
    }

    public function find(string $name, string $form): Pokemon
    {
        $pokemon = $this->pokemonByNameAndForm[$this->createNameKey($name)][$this->createFormKey($form)] ?? null;

        if (!$pokemon instanceof Pokemon) {
            throw new NotFoundException(sprintf('Pokemon with name "%s" and form "%s" not found', $name, $form));
        }

        return $pokemon;
    }

    #[\Override]
    public function getIterator(): \Generator
    {
        foreach ($this->pokemonByNameAndForm as $pokemonByForm) {
            foreach ($pokemonByForm as $pokemon) {
                yield $pokemon;
            }
        }
    }

    private function createNameKey(string $name): string
    {
        return (string) preg_replace('#[^a-z]#', replacement: '', subject: strtolower($name));
    }

    private function createFormKey(string $form): string
    {
        $key = preg_replace('#[^a-z]#', replacement: '', subject: strtolower($form));

        return (string) preg_replace(['#^alola$#'], replacement: ['alolan'], subject: (string) $key);
    }
}
