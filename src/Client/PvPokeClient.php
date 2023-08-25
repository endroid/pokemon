<?php

declare(strict_types=1);

namespace Endroid\Pokemon\Client;

use Endroid\Asset\Factory\AssetFactory;
use Endroid\Pokemon\Model\League;

final class PvPokeClient
{
    public function __construct(
        private AssetFactory $assetFactory
    ) {
    }

    public function getRankingsForLeague(League $league): array
    {
        $cacheAsset = $this->assetFactory->create(null, [
            'url' => 'https://pvpoke.com/data/rankings/all/overall/rankings-'.$league->getMaxCp().'.json',
            'cache_key' => 'pokemon-rankings-'.$league->getMaxCp(),
            'cache_expires_after' => 86400,
        ]);

        $rankings = json_decode($cacheAsset->getData(), true);
        usort($rankings, fn ($a, $b) => $b['score'] <=> $a['score']);

        if (!is_array($rankings)) {
            throw new \RuntimeException('Could not fetch league rankings');
        }

        return $rankings;
    }

    public function getTrainingAnalysisForLeague(League $league): array
    {
        $cacheAsset = $this->assetFactory->create(null, [
            'url' => 'https://pvpoke.com/data/training/analysis/all/'.$league->getMaxCp().'.json',
            'cache_key' => 'pokemon-training-analysis-'.$league->getMaxCp(),
            'cache_expires_after' => 86400,
        ]);

        $trainingAnalysis = json_decode($cacheAsset->getData(), true);

        if (!is_array($trainingAnalysis) || !isset($trainingAnalysis['performers']) || !isset($trainingAnalysis['teams'])) {
            throw new \RuntimeException('Could not fetch league training analysis');
        }

        return $trainingAnalysis;
    }
}
