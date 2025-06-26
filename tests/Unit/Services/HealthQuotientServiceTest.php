<?php

namespace Tests\Unit\Services;

use App\Services\HealthQuotient\HealthQuotientService;
use App\Services\HealthQuotient\DiabetesRiskService;
use App\Services\HealthQuotient\MetabolicSyndromeService;
use App\Services\HealthQuotient\CVDRiskService;
use App\Services\HealthQuotient\MentalHealthService;
use App\Services\HealthQuotient\LifestyleService;
use App\Services\HealthQuotient\FattyLiverService;
use Mockery;
use Tests\TestCase;

class HealthQuotientServiceTest extends TestCase
{
    protected $diabetesService;
    protected $metabolicService;
    protected $cvdService;
    protected $mentalHealthService;
    protected $lifestyleService;
    protected $fattyLiverService;
    protected $healthQuotientService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->diabetesService = Mockery::mock(DiabetesRiskService::class);
        $this->metabolicService = Mockery::mock(MetabolicSyndromeService::class);
        $this->cvdService = Mockery::mock(CVDRiskService::class);
        $this->mentalHealthService = Mockery::mock(MentalHealthService::class);
        $this->lifestyleService = Mockery::mock(LifestyleService::class);
        $this->fattyLiverService = Mockery::mock(FattyLiverService::class);

        // Instantiate service
        $this->healthQuotientService = new HealthQuotientService(
            $this->diabetesService,
            $this->metabolicService,
            $this->cvdService,
            $this->mentalHealthService,
            $this->lifestyleService,
            $this->fattyLiverService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculate_health_quotient_with_all_segments()
    {
        // Arrange
        $inputData = [
            'questionnaire' => [
                'Q1' => 45,
                'Q2' => 'male',
                'Q3' => 'A',
                'Q5' => 'A',
                'Q6' => 'A',
                'Q7' => 'A',
                'Q8' => 'A',
                'Q12' => 'A',
                'Q14' => 'A',
                'Q15' => 'A',
                'Q16' => 'C',
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
                'Q38' => 'A',
                'Q39' => 'A',
                'Q40' => 'A',
                'Q41' => 'C',
                'Q42' => 'C',
                'Q43' => 'A',
                'Q44' => 'A',
                'Q45' => 'A',
                'Q46' => 'A',
                'Q47' => 'A',
                'Q48' => 'A',
                'Q49' => 'B',
                'Q50' => 'A',
                'Q51' => 'A',
                'Q52' => 'A',
            ],
            'biometrics' => [
                'height' => 170,
                'weight' => 70,
                'bmi' => 70 / (1.7 * 1.7),
                'waist_circumference' => 90,
                'systolic_bp' => 120,
                'diastolic_bp' => 80,
            ],
            'lab_tests' => [
                'fbs' => 95,
                'total_cholesterol' => 180,
                'hdl_cholesterol' => 50,
                'triglycerides' => 140,
                'alt' => 30,
                'ast' => 25,
            ],
        ];

        // Mock service responses
        $this->diabetesService->shouldReceive('calculateScore')->andReturn(8);
        $this->metabolicService->shouldReceive('calculateScore')->andReturn(10);
        $this->cvdService->shouldReceive('calculateScore')->andReturn(9);
        $this->mentalHealthService->shouldReceive('calculateScore')->andReturn(10);
        $this->mentalHealthService->shouldReceive('getSubScores')->andReturn([
            'depression' => 10,
            'anxiety' => 10,
            'stress' => 10,
        ]);
        $this->lifestyleService->shouldReceive('calculateScore')->andReturn(10);
        $this->lifestyleService->shouldReceive('getSubScores')->andReturn([
            'nutrition' => 10,
            'physical_activity' => 10,
            'sleep_health' => 10,
            'habits' => 10,
            'stress_wellbeing' => 10,
        ]);
        $this->fattyLiverService->shouldReceive('calculateScore')->andReturn(10);

        // Act
        $result = $this->healthQuotientService->calculateHealthQuotient($inputData);

        // Assert
        $this->assertEquals(94.8, $result['total_hq_score']); // (8*0.2 + 10*0.15 + 9*0.2 + 10*0.2 + 10*0.25 + 10*0.15) * 10
        $this->assertEquals(8, $result['segment_scores']['diabetes_risk']);
        $this->assertEquals(10, $result['segment_scores']['metabolic_syndrome']);
        $this->assertEquals(9, $result['segment_scores']['cvd_risk']);
        $this->assertEquals(10, $result['segment_scores']['mental_health']['score']);
        $this->assertEquals(10, $result['segment_scores']['lifestyle']['score']);
        $this->assertEquals(10, $result['segment_scores']['fatty_liver']);
    }

    public function test_calculate_health_quotient_with_selected_segments()
    {
        // Arrange
        $inputData = [
            'questionnaire' => [
                'Q1' => 45,
                'Q2' => 'male',
                'Q3' => 'A',
                'Q5' => 'A',
                'Q6' => 'A',
                'Q7' => 'A',
                'Q8' => 'A',
                'Q12' => 'A',
                'Q14' => 'A',
                'Q15' => 'A',
                'Q16' => 'C',
            ],
            'biometrics' => [
                'height' => 170,
                'weight' => 70,
                'bmi' => 70 / (1.7 * 1.7),
                'waist_circumference' => 90,
            ],
            'lab_tests' => [
                'fbs' => 95,
            ],
        ];
        $allowedSegments = ['diabetes_risk', 'lifestyle'];

        // Mock service responses
        $this->diabetesService->shouldReceive('calculateScore')->andReturn(8);
        $this->lifestyleService->shouldReceive('calculateScore')->andReturn(10);
        $this->lifestyleService->shouldReceive('getSubScores')->andReturn([
            'nutrition' => 10,
            'physical_activity' => 10,
            'sleep_health' => 10,
            'habits' => 10,
            'stress_wellbeing' => 10,
        ]);

        // Act
        $result = $this->healthQuotientService->calculateHealthQuotient($inputData, $allowedSegments);

        // Assert
        // Weights normalized: diabetes_risk (0.2/0.45 ≈ 0.444), lifestyle (0.25/0.45 ≈ 0.556)
        // Total HQ: (8*0.444 + 10*0.556) * 10 ≈ 91.1
        $this->assertEqualsWithDelta(91.1, $result['total_hq_score'], 0.1);
        $this->assertArrayHasKey('diabetes_risk', $result['segment_scores']);
        $this->assertArrayHasKey('lifestyle', $result['segment_scores']);
        $this->assertEquals(8, $result['segment_scores']['diabetes_risk']);
        $this->assertEquals(10, $result['segment_scores']['lifestyle']['score']);
        $this->assertArrayNotHasKey('metabolic_syndrome', $result['segment_scores']);
        $this->assertArrayNotHasKey('cvd_risk', $result['segment_scores']);
        $this->assertArrayNotHasKey('mental_health', $result['segment_scores']);
        $this->assertArrayNotHasKey('fatty_liver', $result['segment_scores']);
    }

    public function test_calculate_health_quotient_throws_exception_for_missing_input()
    {
        // Arrange
        $inputData = [
            'questionnaire' => [],
            'biometrics' => [],
        ];

        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required input data: questionnaire, biometrics, or lab_tests.');

        // Act
        $this->healthQuotientService->calculateHealthQuotient($inputData);
    }

    public function test_calculate_health_quotient_throws_exception_for_invalid_segments()
    {
        // Arrange
        $inputData = [
            'questionnaire' => ['Q1' => 45],
            'biometrics' => ['bmi' => 24],
            'lab_tests' => ['fbs' => 95],
        ];
        $allowedSegments = ['invalid_segment'];

        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No valid segments provided.');

        // Act
        $this->healthQuotientService->calculateHealthQuotient($inputData, $allowedSegments);
    }
}
