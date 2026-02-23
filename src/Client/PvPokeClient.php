<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Client;

use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pokemon\Model\League;

final readonly class PvPokeClient
{
    public function __construct(
        private AssetFactory $assetFactory,
    ) {}

    /** @return array<mixed> */
    public function getRankingsForLeague(League $league, int $limit): array
    {
        $cacheAsset = $this->assetFactory->create(null, [
            'url' => 'https://pvpoke.com/data/rankings/all/overall/rankings-' . $league->getMaxCp() . '.json',
            'cache_key' => 'pokemon-rankings-' . $league->getMaxCp(),
            'cache_expires_after' => 86_400,
        ]);

        /** @var list<array{score: float, speciesId: string, speciesName: string}>|null $decoded */
        $decoded = json_decode($cacheAsset->getData(), associative: true);

        if (!is_array($decoded)) {
            throw new \RuntimeException('Could not fetch league rankings');
        }

        $rankings = $decoded;
        usort($rankings, static fn(array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice($rankings, offset: 0, length: $limit);
    }

    /** @return array<mixed> */
    public function getTrainingAnalysisForLeague(League $league): array
    {
        $cacheAsset = $this->assetFactory->create(null, [
            'url' => 'https://pvpoke.com/data/training/analysis/all/' . $league->getMaxCp() . '.json',
            'cache_key' => 'pokemon-training-analysis-' . $league->getMaxCp(),
            'cache_expires_after' => 86_400,
        ]);

        /** @var array<string, mixed>|null $trainingAnalysis */
        $trainingAnalysis = json_decode($cacheAsset->getData(), associative: true);

        if (
            !is_array($trainingAnalysis)
            || !array_key_exists('performers', $trainingAnalysis)
            || !array_key_exists('teams', $trainingAnalysis)
        ) {
            throw new \RuntimeException('Could not fetch league training analysis');
        }

        return $trainingAnalysis;
    }
}
