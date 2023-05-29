<?php

namespace Endroid\Pokemon\Model;

final readonly class Level
{
    public const LEVEL_MIN = 1;
    public const LEVEL_MAX = 51;

    public function __construct(
        private int $value
    ) {
        if ($value < self::LEVEL_MIN || $value > self::LEVEL_MAX) {
            throw new \InvalidArgumentException(sprintf('Invalid level value "%s"', $value));
        }
    }

    public static function all(): \Generator
    {
        for ($value = self::LEVEL_MIN; $value <= self::LEVEL_MAX; $value++) {
            yield new self($value);
        }
    }

    public function getCpScalar(): float
    {
        return match($this->value) {
            1 => 0.094,
            2 => 0.16639787,
            3 => 0.21573247,
            4 => 0.25572005,
            5 => 0.29024988,
            6 => 0.3210876,
            7 => 0.34921268,
            8 => 0.37523559,
            9 => 0.39956728,
            10 => 0.42250001,
            11 => 0.44310755,
            12 => 0.46279839,
            13 => 0.48168495,
            14 => 0.49985844,
            15 => 0.51739395,
            16 => 0.53435433,
            17 => 0.55079269,
            18 => 0.56675452,
            19 => 0.58227891,
            20 => 0.59740001,
            21 => 0.61215729,
            22 => 0.62656713,
            23 => 0.64065295,
            24 => 0.65443563,
            25 => 0.667934,
            26 => 0.68116492,
            27 => 0.69414365,
            28 => 0.70688421,
            29 => 0.71939909,
            30 => 0.7317,
            31 => 0.73776948,
            32 => 0.74378943,
            33 => 0.74976104,
            34 => 0.75568551,
            35 => 0.76156384,
            36 => 0.76739717,
            37 => 0.7731865,
            38 => 0.77893275,
            39 => 0.78463697,
            40 => 0.79030001,
            41 => 0.79530001,
            42 => 0.8003,
            43 => 0.8053,
            44 => 0.81029999,
            45 => 0.81529999,
            46 => 0.82029999,
            47 => 0.82529999,
            48 => 0.83029999,
            49 => 0.83529999,
            50 => 0.84029999,
            51 => 0.84529999,
        };
    }
}
