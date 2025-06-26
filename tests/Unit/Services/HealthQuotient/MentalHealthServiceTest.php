<?php

namespace Tests\Unit\Services\HealthQuotient;

use App\Services\HealthQuotient\MentalHealthService;
use Tests\TestCase;

class MentalHealthServiceTest extends TestCase
{
    protected $mentalHealthService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentalHealthService = new MentalHealthService();
    }

    public function test_calculate_mental_health_score_normal()
    {
        // Arrange
        $questionnaire = [
            'Q17' => 0,
            'Q18' => 0,
            'Q19' => 0,
            'Q20' => 0,
            'Q21' => 0,
            'Q22' => 0,
            'Q23' => 0,
            'Q24' => 0,
            'Q25' => 0,
            'Q26' => 0,
            'Q27' => 0,
            'Q28' => 0,
            'Q29' => 0,
            'Q30' => 0,
            'Q31' => 0,
            'Q32' => 0,
            'Q33' => 0,
            'Q34' => 0,
            'Q35' => 0,
            'Q36' => 0,
            'Q37' => 0,
        ];

        // Act
        $score = $this->mentalHealthService->calculateScore($questionnaire);
        $subScores = $this->mentalHealthService->getSubScores();

        // Assert
        $this->assertEquals(10, $score); // Normal for all subscales
        $this->assertEquals(['depression' => 10, 'anxiety' => 10, 'stress' => 10], $subScores);
    }

    public function test_calculate_mental_health_score_severe()
    {
        // Arrange
        $questionnaire = [
            'Q17' => 3,
            'Q18' => 3,
            'Q19' => 3,
            'Q20' => 3,
            'Q21' => 3,
            'Q22' => 3,
            'Q23' => 3,
            'Q24' => 3,
            'Q25' => 3,
            'Q26' => 3,
            'Q27' => 3,
            'Q28' => 3,
            'Q29' => 3,
            'Q30' => 3,
            'Q31' => 3,
            'Q32' => 3,
            'Q33' => 3,
            'Q34' => 3,
            'Q35' => 3,
            'Q36' => 3,
            'Q37' => 3,
        ];

        // Act
        $score = $this->mentalHealthService->calculateScore($questionnaire);
        $subScores = $this->mentalHealthService->getSubScores();

        // Assert
        $this->assertEquals(0, $score); // Extremely severe for all subscales
        $this->assertEquals(['depression' => 0, 'anxiety' => 0, 'stress' => 0], $subScores);
    }
}
