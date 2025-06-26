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
}
