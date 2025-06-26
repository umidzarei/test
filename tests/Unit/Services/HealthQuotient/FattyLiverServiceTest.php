<?php

namespace Tests\Unit\Services\HealthQuotient;

use App\Services\HealthQuotient\FattyLiverService;
use Tests\TestCase;

class FattyLiverServiceTest extends TestCase
{
    protected $fattyLiverService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fattyLiverService = new FattyLiverService();
    }

    public function test_calculate_fatty_liver_score_low_risk()
    {
        // Arrange
        $questionnaire = [
            'Q2' => 'male',
            'Q5' => 'A', // No high glucose
            'Q14' => 'A', // No alcohol
            'Q15' => 'A',
        ];
        $biometrics = [
            'bmi' => 24,
        ];
        $labTests = [
            'alt' => 30,
            'ast' => 25,
            'fbs' => 95,
        ];

        // Act
        $score = $this->fattyLiverService->calculateScore($questionnaire, $biometrics, $labTests);

        // Assert
        $this->assertEquals(10, $score); // Low risk
    }

    public function test_calculate_fatty_liver_score_high_risk()
    {
        // Arrange
        $questionnaire = [
            'Q2' => 'female',
            'Q5' => 'B', // High glucose
            'Q14' => 'E', // Heavy alcohol
            'Q15' => 'C',
        ];
        $biometrics = [
            'bmi' => 31,
        ];
        $labTests = [
            'alt' => 90, // Significantly elevated
            'ast' => 25,
            'fbs' => 130,
        ];

        // Act
        $score = $this->fattyLiverService->calculateScore($questionnaire, $biometrics, $labTests);

        // Assert
        $this->assertEquals(1, $score); // High risk
    }

    public function test_calculate_fatty_liver_score_indeterminate()
    {
        // Arrange
        $questionnaire = [
            'Q2' => 'male',
            'Q5' => 'A',
            'Q14' => 'A',
            'Q15' => 'A',
        ];
        $biometrics = [
            'bmi' => 24,
        ];
        $labTests = [
            'alt' => 30,
            'ast' => 0, // Missing AST
            'fbs' => 95,
        ];

        // Act
        $score = $this->fattyLiverService->calculateScore($questionnaire, $biometrics, $labTests);

        // Assert
        $this->assertEquals(4, $score); // Indeterminate due to missing AST
    }
}
