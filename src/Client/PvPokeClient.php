<?php

namespace Endroid\Pokemon\Client;

use Endroid\Pokemon\Model\League;

final class PvPokeClient
{
    public function getTrainingAnalysisForLeague(League $league): array
    {
        $trainingAnalysis = json_decode((string) file_get_contents('https://pvpoke.com/data/training/analysis/all/'.$league->getMaxCp().'.json'), true);

        if (!is_array($trainingAnalysis) || !isset($trainingAnalysis['performers']) || !isset($trainingAnalysis['teams'])) {
            throw new \RuntimeException('Could not fetch league training analysis');
        }

        return $trainingAnalysis;
    }
}
