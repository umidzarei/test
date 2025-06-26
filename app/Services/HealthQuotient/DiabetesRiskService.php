<?php

namespace App\Services\HealthQuotient;

class DiabetesRiskService
{
    /**
     * Calculate Diabetes Risk Score (FINDRISC-based).
     *
     * @param array $questionnaire
     * @param array $biometrics
     * @return float
     */
    public function calculateScore(array $questionnaire, array $biometrics): float
    {
        $rawScore = $this->calculateRawFINDRISCScore($questionnaire, $biometrics);
        return $this->mapToSegmentScore($rawScore);
    }

    /**
     * Calculate raw FINDRISC score.
     *
     * @param array $questionnaire
     * @param array $biometrics
     * @return int
     */
    protected function calculateRawFINDRISCScore(array $questionnaire, array $biometrics): int
    {
        $agePoints = $this->getAgePoints($questionnaire['Q1'] ?? 0);
        $bmiPoints = $this->getBMIPoints($biometrics['bmi'] ?? 0);
        $waistPoints = $this->getWaistPoints(
            $biometrics['waist_circumference'] ?? 0,
            $questionnaire['Q2'] ?? ''
        );
        $activityPoints = $this->getActivityPoints($questionnaire['Q8'] ?? '');
        $nutritionPoints = $this->getNutritionPoints($questionnaire['Q7'] ?? '');
        $bpMedsPoints = $this->getBPMedsPoints($questionnaire['Q6'] ?? '');
        $highGlucosePoints = $this->getHighGlucosePoints($questionnaire['Q5'] ?? '');
        $familyDiabetesPoints = $this->getFamilyDiabetesPoints($questionnaire['Q3'] ?? '');

        return $agePoints + $bmiPoints + $waistPoints + $activityPoints +
            $nutritionPoints + $bpMedsPoints + $highGlucosePoints + $familyDiabetesPoints;
    }

    protected function getAgePoints(int $age): int
    {
        if ($age < 45)
            return 0;
        if ($age <= 54)
            return 2;
        if ($age <= 64)
            return 3;
        return 4;
    }

    protected function getBMIPoints(float $bmi): int
    {
        if ($bmi < 25)
            return 0;
        if ($bmi <= 30)
            return 1;
        return 3;
    }

    protected function getWaistPoints(float $waist, string $gender): int
    {
        if ($gender === 'male') {
            if ($waist < 94)
                return 0;
            if ($waist <= 102)
                return 3;
            return 4;
        } else {
            if ($waist < 80)
                return 0;
            if ($waist <= 88)
                return 3;
            return 4;
        }
    }

    protected function getActivityPoints(string $activity): int
    {
        return $activity === 'A' ? 0 : 2;
    }

    protected function getNutritionPoints(string $nutrition): int
    {
        return $nutrition === 'A' ? 0 : 1;
    }

    protected function getBPMedsPoints(string $bpMeds): int
    {
        return $bpMeds === 'A' ? 0 : 2;
    }

    protected function getHighGlucosePoints(string $highGlucose): int
    {
        return $highGlucose === 'A' ? 0 : 5;
    }

    protected function getFamilyDiabetesPoints(string $familyHistory): int
    {
        if ($familyHistory === 'A')
            return 0;
        if ($familyHistory === 'B')
            return 3;
        return 5;
    }

    /**
     * Map raw FINDRISC score to segment score (0-10).
     *
     * @param int $rawScore
     * @return float
     */
    protected function mapToSegmentScore(int $rawScore): float
    {
        if ($rawScore < 7)
            return 10;
        if ($rawScore <= 11)
            return 8;
        if ($rawScore <= 14)
            return 6;
        if ($rawScore <= 20)
            return 3;
        return 0;
    }
}
