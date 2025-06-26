<?php

namespace App\Services\HealthQuotient;

class MentalHealthService
{
    protected $subScores = [];

    /**
     * Calculate Mental Health Score (DASS-21-based).
     *
     * @param array $questionnaire
     * @return float
     */
    public function calculateScore(array $questionnaire): float
    {
        $depressionScore = $this->calculateDepressionScore($questionnaire);
        $anxietyScore = $this->calculateAnxietyScore($questionnaire);
        $stressScore = $this->calculateStressScore($questionnaire);

        $this->subScores = [
            'depression' => $depressionScore,
            'anxiety' => $anxietyScore,
            'stress' => $stressScore,
        ];

        return ($depressionScore + $anxietyScore + $stressScore) / 3;
    }

    /**
     * Get sub-scores for depression, anxiety, and stress.
     *
     * @return array
     */
    public function getSubScores(): array
    {
        return $this->subScores;
    }

    protected function calculateDepressionScore(array $questionnaire): float
    {
        $rawScore = 2 * array_sum([
            $questionnaire['Q19'] ?? 0,
            $questionnaire['Q21'] ?? 0,
            $questionnaire['Q26'] ?? 0,
            $questionnaire['Q29'] ?? 0,
            $questionnaire['Q32'] ?? 0,
            $questionnaire['Q33'] ?? 0,
            $questionnaire['Q37'] ?? 0,
        ]);

        return $this->mapToSubScore($rawScore, 'depression');
    }

    protected function calculateAnxietyScore(array $questionnaire): float
    {
        $rawScore = 2 * array_sum([
            $questionnaire['Q18'] ?? 0,
            $questionnaire['Q20'] ?? 0,
            $questionnaire['Q23'] ?? 0,
            $questionnaire['Q25'] ?? 0,
            $questionnaire['Q31'] ?? 0,
            $questionnaire['Q35'] ?? 0,
            $questionnaire['Q36'] ?? 0,
        ]);

        return $this->mapToSubScore($rawScore, 'anxiety');
    }

    protected function calculateStressScore(array $questionnaire): float
    {
        $rawScore = 2 * array_sum([
            $questionnaire['Q17'] ?? 0,
            $questionnaire['Q22'] ?? 0,
            $questionnaire['Q24'] ?? 0,
            $questionnaire['Q27'] ?? 0,
            $questionnaire['Q28'] ?? 0,
            $questionnaire['Q30'] ?? 0,
            $questionnaire['Q34'] ?? 0,
        ]);

        return $this->mapToSubScore($rawScore, 'stress');
    }

    protected function mapToSubScore(int $rawScore, string $type): float
    {
        $cutoffs = [
            'depression' => [
                [0, 9, 10],
                [10, 13, 7],
                [14, 20, 4],
                [21, 27, 2],
                [28, PHP_INT_MAX, 0],
            ],
            'anxiety' => [
                [0, 7, 10],
                [8, 9, 7],
                [10, 14, 4],
                [15, 19, 2],
                [20, PHP_INT_MAX, 0],
            ],
            'stress' => [
                [0, 14, 10],
                [15, 18, 7],
                [19, 25, 4],
                [26, 33, 2],
                [34, PHP_INT_MAX, 0],
            ],
        ];

        foreach ($cutoffs[$type] as [$min, $max, $score]) {
            if ($rawScore >= $min && $rawScore <= $max) {
                return $score;
            }
        }

        return 0;
    }
}
