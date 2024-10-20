<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Model;

final readonly class Iv
{
    final public const IV_MIN = 0;
    final public const IV_MAX = 15;

    public function __construct(
        public int $value,
    ) {
        if ($value < self::IV_MIN || $value > self::IV_MAX) {
            throw new \InvalidArgumentException(sprintf('Invalid IV value "%s"', $value));
        }
    }

    public static function all(): \Generator
    {
        for ($value = self::IV_MIN; $value <= self::IV_MAX; ++$value) {
            yield new self($value);
        }
    }

    public static function max(): self
    {
        return new self(self::IV_MAX);
    }
}
