<?php

namespace App\Services\HealthQuotient;

use App\Services\HealthQuotient\CVDRiskService;
use App\Services\HealthQuotient\DiabetesRiskService;
use App\Services\HealthQuotient\FattyLiverService;
use App\Services\HealthQuotient\LifestyleService;
use App\Services\HealthQuotient\MentalHealthService;
use App\Services\HealthQuotient\MetabolicSyndromeService;
use InvalidArgumentException;

class HealthQuotientService
{
    protected $diabetesService;
    protected $metabolicService;
    protected $cvdService;
    protected $mentalHealthService;
    protected $lifestyleService;
    protected $fattyLiverService;

    public function __construct(
        DiabetesRiskService $diabetesService,
        MetabolicSyndromeService $metabolicService,
        CVDRiskService $cvdService,
        MentalHealthService $mentalHealthService,
        LifestyleService $lifestyleService,
        FattyLiverService $fattyLiverService
    ) {
        $this->diabetesService = $diabetesService;
        $this->metabolicService = $metabolicService;
        $this->cvdService = $cvdService;
        $this->mentalHealthService = $mentalHealthService;
        $this->lifestyleService = $lifestyleService;
        $this->fattyLiverService = $fattyLiverService;
    }

    public function calculateHealthQuotient(array $inputData, array $allowedSegments = []): array
    {
        // Validate input
        if (
            !isset($inputData['questionnaire']) ||
            !isset($inputData['biometrics']) ||
            !isset($inputData['lab_tests'])
        ) {
            throw new InvalidArgumentException('Missing required input data: questionnaire, biometrics, or lab_tests.');
        }

        // Extract input
        $questionnaire = $inputData['questionnaire'];
        $biometrics = $inputData['biometrics'];
        $labTests = $inputData['lab_tests'];

        // Define all possible segments with their weights
        $segments = [
            'diabetes_risk' => ['service' => $this->diabetesService, 'method' => 'calculateScore', 'weight' => 0.2, 'args' => [$questionnaire, $biometrics]],
            'metabolic_syndrome' => ['service' => $this->metabolicService, 'method' => 'calculateScore', 'weight' => 0.15, 'args' => [$questionnaire, $biometrics, $labTests]],
            'cvd_risk' => ['service' => $this->cvdService, 'method' => 'calculateScore', 'weight' => 0.2, 'args' => [$questionnaire, $biometrics, $labTests]],
            'mental_health' => ['service' => $this->mentalHealthService, 'method' => 'calculateScore', 'weight' => 0.2, 'args' => [$questionnaire], 'sub_scores' => 'getSubScores'],
            'lifestyle' => ['service' => $this->lifestyleService, 'method' => 'calculateScore', 'weight' => 0.25, 'args' => [$questionnaire], 'sub_scores' => 'getSubScores'],
            'fatty_liver' => ['service' => $this->fattyLiverService, 'method' => 'calculateScore', 'weight' => 0.15, 'args' => [$questionnaire, $biometrics, $labTests]],
        ];

        // Filter segments based on allowedSegments
        if (!empty($allowedSegments)) {
            $segments = array_intersect_key($segments, array_flip($allowedSegments));
            if (empty($segments)) {
                throw new InvalidArgumentException('No valid segments provided.');
            }
        }

        // Normalize weights if not all segments are included
        $totalWeight = array_sum(array_column($segments, 'weight'));
        foreach ($segments as &$segment) {
            $segment['weight'] = $segment['weight'] / $totalWeight;
        }
        unset($segment);

        // Calculate segment scores
        $segmentScores = [];
        $weightedSum = 0;

        foreach ($segments as $key => $config) {
            $score = call_user_func_array([$config['service'], $config['method']], $config['args']);
            $weightedSum += $score * $config['weight'];

            $segmentScores[$key] = $score;
            if (isset($config['sub_scores'])) {
                $segmentScores[$key] = [
                    'score' => $score,
                    'sub_scores' => call_user_func([$config['service'], $config['sub_scores']]),
                ];
            }
        }

        $totalHQScore = $weightedSum * 10; // Scale to 0-100

        // Prepare result
        return [
            'total_hq_score' => round($totalHQScore, 1),
            'segment_scores' => $segmentScores,
        ];
    }
}
