<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

enum Type: string
{
    case Bug = 'bug';
    case Dark = 'dark';
    case Dragon = 'dragon';
    case Electric = 'electric';
    case Fairy = 'fairy';
    case Fighting = 'fighting';
    case Fire = 'fire';
    case Flying = 'flying';
    case Ghost = 'ghost';
    case Grass = 'grass';
    case Ground = 'ground';
    case Ice = 'ice';
    case Normal = 'normal';
    case Poison = 'poison';
    case Psychic = 'psychic';
    case Rock = 'rock';
    case Steel = 'steel';
    case Water = 'water';

    public function getColorCode(): string
    {
        return match ($this) {
            self::Bug => '#A8B820',
            self::Dark => '#705848',
            self::Dragon => '#7038F8',
            self::Electric => '#F8D030',
            self::Fairy => '#EE99AC',
            self::Fighting => '#C03028',
            self::Fire => '#F08030',
            self::Flying => '#A890F0',
            self::Ghost => '#705898',
            self::Grass => '#78C850',
            self::Ground => '#E0C068',
            self::Ice => '#98D8D8',
            self::Normal => '#A8A878',
            self::Poison => '#A040A0',
            self::Psychic => '#F85888',
            self::Rock => '#B8A038',
            self::Steel => '#B8B8D0',
            self::Water => '#6890F0',
        };
    }
}
