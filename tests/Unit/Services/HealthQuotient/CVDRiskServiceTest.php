<?php

namespace Tests\Unit\Services\HealthQuotient;

use App\Services\HealthQuotient\CVDRiskService;
use Mockery;
use Tests\TestCase;

class CVDRiskServiceTest extends TestCase
{
    protected $cvdService;

    protected function setUp(): void
    {
        parent::setUp();
        // Allow mocking protected methods
        $this->cvdService = Mockery::mock(CVDRiskService::class)->shouldAllowMockingProtectedMethods()->makePartial();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculate_cvd_score_low_risk()
    {
        // Arrange
        $questionnaire = [
            'Q1' => 40,
            'Q2' => 'male',
            'Q5' => 'A',
            'Q6' => 'A',
            'Q12' => 'A',
        ];
        $biometrics = [
            'systolic_bp' => 120,
        ];
        $labTests = [
            'total_cholesterol' => 180,
            'hdl_cholesterol' => 50,
            'fbs' => 95,
        ];

        // Mock protected methods
        $this->cvdService->shouldReceive('calculateFraminghamPoints')->once()->andReturn(0);
        $this->cvdService->shouldReceive('convertToRiskPercentage')->once()->andReturn(4);

        // Act
        $score = $this->cvdService->calculateScore($questionnaire, $biometrics, $labTests);

        // Assert
        $this->assertEquals(10, $score); // Low risk (< 5%)
    }

    public function test_calculate_cvd_score_high_risk()
    {
        // Arrange
        $questionnaire = [
            'Q1' => 65,
            'Q2' => 'male',
            'Q5' => 'B',
            'Q6' => 'B',
            'Q12' => 'D',
        ];
        $biometrics = [
            'systolic_bp' => 140,
        ];
        $labTests = [
            'total_cholesterol' => 240,
            'hdl_cholesterol' => 30,
            'fbs' => 130,
        ];

        // Mock protected methods
        $this->cvdService->shouldReceive('calculateFraminghamPoints')->once()->andReturn(20);
        $this->cvdService->shouldReceive('convertToRiskPercentage')->once()->andReturn(35);

        // Act
        $score = $this->cvdService->calculateScore($questionnaire, $biometrics, $labTests);

        // Assert
        $this->assertEquals(0, $score); // High risk (>= 30%)
    }
    public function it_calculates_the_correct_framingham_points_and_risk_end_to_end()
    {
        $realCvdService = new CVDRiskService();

        $questionnaire = [
            'Q1' => 40,      // Age
            'Q2' => 'male',  // Gender
            'Q6' => 'A',     // Not treated for BP
            'Q12' => 'A',    // Not a smoker
        ];
        $biometrics = [
            'systolic_bp' => 125,
        ];
        $labTests = [
            'total_cholesterol' => 180,
            'hdl' => 55,
        ];

        /*
         * Expected Manual Calculation:
         * Age (40-44, male): 5 points
         * Total Cholesterol (40-49, 160-199, male): 1 point
         * HDL (50-59, male): 0 points
         * Systolic BP (120-129, untreated, male): 1 point
         * Smoker: 0 points
         * TOTAL POINTS = 5 + 1 + 0 + 1 + 0 = 7 points
         * Risk for 7 points (male): 3%
         * Final Score (mapToSegmentScore for 3%): 10.0
         */


        $score = $realCvdService->calculateScore($questionnaire, $biometrics, $labTests);


        $this->assertEquals(10.0, $score);
    }
}
