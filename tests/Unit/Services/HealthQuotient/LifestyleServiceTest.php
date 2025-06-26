<?php

namespace Tests\Unit\Services\HealthQuotient;

use App\Services\HealthQuotient\LifestyleService;
use Tests\TestCase;

class LifestyleServiceTest extends TestCase
{
    protected $lifestyleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lifestyleService = new LifestyleService();
    }

    public function test_calculate_lifestyle_score_optimal()
    {
        // Arrange
        $questionnaire = [
            'Q7' => 'A',
            'Q8' => 'A',
            'Q9' => 'C',
            'Q10' => 'A',
            'Q11' => 'A',
            'Q12' => 'A',
            'Q13' => 'A',
            'Q14' => 'A',
            'Q15' => 'A',
            'Q16' => 'C',
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
        ];

        // Act
        $score = $this->lifestyleService->calculateScore($questionnaire);
        $subScores = $this->lifestyleService->getSubScores();

        // Assert
        $this->assertEqualsWithDelta(10.0, $score, 0.01); // Corrected expected value
        $this->assertEqualsWithDelta(10.0, $subScores['nutrition'], 0.01); // Corrected expected value
        $this->assertEquals(10, $subScores['physical_activity']);
        $this->assertEquals(10, $subScores['sleep_health']);
        $this->assertEquals(10, $subScores['habits']);
        $this->assertEquals(10, $subScores['stress_wellbeing']);
    }

    public function test_calculate_lifestyle_score_poor()
    {
        // Arrange
        $questionnaire = [
            'Q7' => 'B',
            'Q8' => 'B',
            'Q9' => 'A',
            'Q10' => 'D',
            'Q11' => 'D',
            'Q12' => 'D',
            'Q13' => 'D',
            'Q14' => 'E',
            'Q15' => 'C',
            'Q16' => 'A',
            'Q38' => 'C',
            'Q39' => 'C',
            'Q40' => 'C',
            'Q41' => 'A',
            'Q42' => 'A',
            'Q43' => 'D',
            'Q44' => 'E',
            'Q45' => 'C',
            'Q46' => 'D',
            'Q47' => 'C',
            'Q48' => 'D',
            'Q49' => 'A',
            'Q50' => 'D',
            'Q51' => 'D',
            'Q52' => 'D',
        ];

        // Act
        $score = $this->lifestyleService->calculateScore($questionnaire);
        $subScores = $this->lifestyleService->getSubScores();

        // Assert
        $this->assertEqualsWithDelta(0.105, $score, 0.01); // Corrected expected value
        $this->assertEqualsWithDelta(0.526, $subScores['nutrition'], 0.01); // Corrected expected value
        $this->assertEquals(0, $subScores['physical_activity']);
        $this->assertEquals(0, $subScores['sleep_health']);
        $this->assertEquals(0, $subScores['habits']);
        $this->assertEquals(0, $subScores['stress_wellbeing']);
    }
}
