<?php

namespace Tests\Unit\Services\HealthQuotient;

use App\Services\HealthQuotient\DiabetesRiskService;
use Tests\TestCase;

class DiabetesRiskServiceTest extends TestCase
{
    protected $diabetesService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->diabetesService = new DiabetesRiskService();
    }

    public function test_calculate_diabetes_score_low_risk()
    {
        // Arrange
        $questionnaire = [
            'Q1' => 40, // Age < 45
            'Q2' => 'male',
            'Q3' => 'A', // No family history
            'Q5' => 'A', // No high glucose
            'Q6' => 'A', // No BP meds
            'Q7' => 'A', // Daily fruit/veg
            'Q8' => 'A', // Active
        ];
        $biometrics = [
            'bmi' => 24, // < 25
            'waist_circumference' => 90, // < 94
        ];

        // Act
        $score = $this->diabetesService->calculateScore($questionnaire, $biometrics);

        // Assert
        $this->assertEquals(10, $score); // Low risk
    }

    public function test_calculate_diabetes_score_high_risk()
    {
        // Arrange
        $questionnaire = [
            'Q1' => 65, // Age >= 65
            'Q2' => 'female',
            'Q3' => 'C', // Strong family history
            'Q5' => 'B', // High glucose
            'Q6' => 'B', // BP meds
            'Q7' => 'B', // Not daily fruit/veg
            'Q8' => 'B', // Not active
        ];
        $biometrics = [
            'bmi' => 31, // > 30
            'waist_circumference' => 90, // > 88
        ];

        // Act
        $score = $this->diabetesService->calculateScore($questionnaire, $biometrics);

        // Assert
        $this->assertEquals(0, $score); // Very high risk (4+3+4+2+1+2+5+5 = 26)
    }
}
