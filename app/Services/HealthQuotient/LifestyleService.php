<?php

namespace App\Services\HealthQuotient;

class LifestyleService
{
    protected $subScores = [];

    /**
     * Calculate Lifestyle Score.
     *
     * @param array $questionnaire
     * @return float
     */
    public function calculateScore(array $questionnaire): float
    {
        $nutritionScore = $this->calculateNutritionScore($questionnaire);
        $activityScore = $this->calculateActivityScore($questionnaire);
        $sleepScore = $this->calculateSleepScore($questionnaire);
        $habitsScore = $this->calculateHabitsScore($questionnaire);
        $stressWellbeingScore = $this->calculateStressWellbeingScore($questionnaire);

        $this->subScores = [
            'nutrition' => $nutritionScore,
            'physical_activity' => $activityScore,
            'sleep_health' => $sleepScore,
            'habits' => $habitsScore,
            'stress_wellbeing' => $stressWellbeingScore,
        ];

        return ($nutritionScore + $activityScore + $sleepScore + $habitsScore + $stressWellbeingScore) / 5;
    }

    /**
     * Get sub-scores for lifestyle segments.
     *
     * @return array
     */
    public function getSubScores(): array
    {
        return $this->subScores;
    }

    protected function calculateNutritionScore(array $questionnaire): float
    {
        $rawScore = 0;

        // Q7: Fruit & Vegetable Intake
        $rawScore += ($questionnaire['Q7'] ?? '') === 'A' ? 2 : 0;

        // Q48: Servings of Fruit & Veg
        $q48 = $questionnaire['Q48'] ?? '';
        $rawScore += match ($q48) {
            'A' => 3,
            'B' => 2,
            'C' => 1,
            'D' => 0,
            default => 0,
        };

        // Q38: Whole Grains
        $q38 = $questionnaire['Q38'] ?? '';
        $rawScore += match ($q38) {
            'A' => 2,
            'B' => 1,
            'C' => 0,
            default => 0,
        };

        // Q39: Red/Processed Meat
        $q39 = $questionnaire['Q39'] ?? '';
        $rawScore += match ($q39) {
            'A' => 2,
            'B' => 1,
            'C' => 0,
            default => 0,
        };

        // Q49: White Meat
        $q49 = $questionnaire['Q49'] ?? '';
        $rawScore += match ($q49) {
            'A' => 1,
            'B', 'C' => 2,
            'D' => 1,
            default => 0,
        };

        // Q40: Fish
        $q40 = $questionnaire['Q40'] ?? '';
        $rawScore += match ($q40) {
            'A' => 2,
            'B' => 1,
            'C' => 0,
            default => 0,
        };

        // Q10: Sugary Drinks
        $q10 = $questionnaire['Q10'] ?? '';
        $rawScore += match ($q10) {
            'A' => 2,
            'B' => 1,
            'C', 'D' => 0,
            default => 0,
        };

        // Q11: Processed Foods
        $q11 = $questionnaire['Q11'] ?? '';
        $rawScore += match ($q11) {
            'A' => 2,
            'B' => 1,
            'C', 'D' => 0,
            default => 0,
        };

        // Q41: Water Intake
        $q41 = $questionnaire['Q41'] ?? '';
        $rawScore += match ($q41) {
            'C', 'D' => 2,
            'B' => 1,
            'A' => 0,
            default => 0,
        };

        return ($rawScore / 19) * 10;
    }

    protected function calculateActivityScore(array $questionnaire): float
    {
        $rawScore = 0;

        // Q8: Daily Routine Activity
        $rawScore += ($questionnaire['Q8'] ?? '') === 'A' ? 2 : 0;

        // Q9: Dedicated Exercise
        $q9 = $questionnaire['Q9'] ?? '';
        $rawScore += match ($q9) {
            'C' => 4,
            'D' => 3,
            'B' => 2,
            'A' => 0,
            default => 0,
        };

        // Q42: Strength Training
        $q42 = $questionnaire['Q42'] ?? '';
        $rawScore += match ($q42) {
            'C', 'D' => 2,
            'B' => 1,
            'A' => 0,
            default => 0,
        };

        // Q43: Sedentary Time
        $q43 = $questionnaire['Q43'] ?? '';
        $rawScore += match ($q43) {
            'A' => 2,
            'B' => 1,
            'C', 'D' => 0,
            default => 0,
        };

        return ($rawScore / 10) * 10;
    }

    protected function calculateSleepScore(array $questionnaire): float
    {
        $rawScore = 0;

        // Q16: Sleep Duration
        $q16 = $questionnaire['Q16'] ?? '';
        $rawScore += match ($q16) {
            'C' => 3,
            'B', 'D' => 1,
            'A' => 0,
            default => 0,
        };

        // Q44: Sleep Quality
        $q44 = $questionnaire['Q44'] ?? '';
        $rawScore += match ($q44) {
            'A' => 3,
            'B' => 2,
            'C' => 1,
            'D', 'E' => 0,
            default => 0,
        };

        return ($rawScore / 6) * 10;
    }

    protected function calculateHabitsScore(array $questionnaire): float
    {
        $rawScore = 0;

        // Tobacco (Q12, Q13)
        $q12 = $questionnaire['Q12'] ?? '';
        $q13 = $questionnaire['Q13'] ?? '';
        if (in_array($q12, ['A', 'B']) && in_array($q13, ['A', 'B'])) {
            $rawScore += 4;
        } elseif (in_array($q12, ['D']) || in_array($q13, ['D'])) {
            $rawScore += 0;
        } else {
            $rawScore += 2;
        }

        // Alcohol (Q14, Q15)
        $q14 = $questionnaire['Q14'] ?? '';
        $q15 = $questionnaire['Q15'] ?? '';
        if ($q14 === 'A' || (in_array($q14, ['B', 'C']) && $q15 === 'B')) {
            $rawScore += 3;
        } elseif ($q14 === 'D' && $q15 === 'B') {
            $rawScore += 2;
        } else {
            $rawScore += 0;
        }

        // Checkups (Q47)
        $q47 = $questionnaire['Q47'] ?? '';
        $rawScore += match ($q47) {
            'A' => 2,
            'B' => 1,
            'C' => 0,
            default => 0,
        };

        // Willingness (Q50)
        $q50 = $questionnaire['Q50'] ?? '';
        $rawScore += in_array($q50, ['A', 'B']) ? 1 : 0;

        return ($rawScore / 10) * 10;
    }

    protected function calculateStressWellbeingScore(array $questionnaire): float
    {
        $rawScore = 0;

        // Q45: Hobbies/Relaxation
        $q45 = $questionnaire['Q45'] ?? '';
        $rawScore += match ($q45) {
            'A' => 3,
            'B' => 1,
            'C' => 0,
            default => 0,
        };

        // Q46: Social Connection
        $q46 = $questionnaire['Q46'] ?? '';
        $rawScore += match ($q46) {
            'A' => 3,
            'B' => 1,
            'C', 'D' => 0,
            default => 0,
        };

        // Q51: Stress Reduction
        $q51 = $questionnaire['Q51'] ?? '';
        $rawScore += match ($q51) {
            'A' => 2,
            'B' => 1,
            'C', 'D' => 0,
            default => 0,
        };

        // Q52: Optimistic Outlook
        $q52 = $questionnaire['Q52'] ?? '';
        $rawScore += match ($q52) {
            'A' => 2,
            'B' => 1,
            'C', 'D' => 0,
            default => 0,
        };

        return ($rawScore / 10) * 10;
    }
}
