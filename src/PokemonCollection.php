<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

use Endroid\Pokemon\Model\Pokemon;

final class PokemonCollection implements \IteratorAggregate
{
    private array $pokemonByNumber = [];
    private array $pokemonByNameKey = [];

    public function add(Pokemon $pokemon): void
    {
        $this->pokemonByNumber[$pokemon->number] = $pokemon;
        $this->pokemonByNameKey[$this->createNameKey($pokemon->name)] = $pokemon;
    }

    public function findByNumber(int $number): Pokemon
    {
        $pokemon = $this->pokemonByNumber[$number] ?? null;

        if (!$pokemon instanceof Pokemon) {
            throw new \Exception(sprintf('Pokemon with number "%s" not found', $number));
        }

        return $pokemon;
    }

    public function findByName(string $name): Pokemon
    {
        $pokemon = $this->pokemonByNameKey[$this->createNameKey($name)] ?? null;

        if (!$pokemon instanceof Pokemon) {
            throw new \Exception(sprintf('Pokemon with name "%s" not found', $name));
        }

        return $pokemon;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->pokemonByNumber);
    }

    private function createNameKey(string $name): string
    {
        return preg_replace('#[^a-z]#', '', strtolower($name));
    }
}
