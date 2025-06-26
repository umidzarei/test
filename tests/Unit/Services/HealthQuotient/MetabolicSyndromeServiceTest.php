<?php

namespace Tests\Unit\Services\HealthQuotient;

use App\Services\HealthQuotient\MetabolicSyndromeService;
use Tests\TestCase;

class MetabolicSyndromeServiceTest extends TestCase
{
    protected $metabolicService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metabolicService = new MetabolicSyndromeService();
    }

    public function test_calculate_metabolic_score_no_syndrome()
    {
        // Arrange
        $questionnaire = [
            'Q2' => 'male',
            'Q5' => 'A', // No high glucose
            'Q6' => 'A', // No BP meds
        ];
        $biometrics = [
            'waist_circumference' => 90, // < 95
            'systolic_bp' => 120,
            'diastolic_bp' => 80,
        ];
        $labTests = [
            'triglycerides' => 140, // < 150
            'hdl_cholesterol' => 50, // >= 40
            'fbs' => 95, // < 100
        ];

        // Act
        $score = $this->metabolicService->calculateScore($questionnaire, $biometrics, $labTests);

        // Assert
        $this->assertEquals(10, $score); // No metabolic syndrome
    }

    public function test_calculate_metabolic_score_with_syndrome()
    {
        // Arrange
        $questionnaire = [
            'Q2' => 'female',
            'Q5' => 'B', // High glucose
            'Q6' => 'B', // BP meds
        ];
        $biometrics = [
            'waist_circumference' => 100, // >= 95
            'systolic_bp' => 140,
            'diastolic_bp' => 90,
        ];
        $labTests = [
            'triglycerides' => 160, // >= 150
            'hdl_cholesterol' => 45, // < 50
            'fbs' => 110, // >= 100
        ];

        // Act
        $score = $this->metabolicService->calculateScore($questionnaire, $biometrics, $labTests);

        // Assert
        $this->assertEquals(2, $score); // Has metabolic syndrome
    }
}
