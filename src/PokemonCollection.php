<?php

declare(strict_types=1);

namespace Endroid\Pokemon;

use Endroid\Pokemon\Model\Pokemon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PokemonCollection implements \IteratorAggregate
{
    private array $pokemonByNameAndForm = [];

    public function add(Pokemon $pokemon): void
    {
        $this->pokemonByNameAndForm[$this->createNameKey($pokemon->name)][$this->createFormKey($pokemon->form)] = $pokemon;
    }

    public function find(string $name, string $form): Pokemon
    {
        $pokemon = $this->pokemonByNameAndForm[$this->createNameKey($name)][$this->createFormKey($form)] ?? null;

        if (!$pokemon instanceof Pokemon) {
            throw new NotFoundHttpException(sprintf('Pokemon with name "%s" and form "%s" not found', $name, $form));
        }

        return $pokemon;
    }

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
        return preg_replace('#[^a-z]#', '', strtolower($name));
    }

    private function createFormKey(string $form): string
    {
        $key = preg_replace('#[^a-z]#', '', strtolower($form));

        return preg_replace(['#^alola$#'], ['alolan'], $key);
    }
}
