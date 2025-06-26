<?php

namespace App\Services\HealthQuotient;

class MetabolicSyndromeService
{
    /**
     * Calculate Metabolic Syndrome Score (IDF-based).
     *
     * @param array $questionnaire
     * @param array $biometrics
     * @param array $labTests
     * @return float
     */
    public function calculateScore(array $questionnaire, array $biometrics, array $labTests): float
    {
        $hasCentralObesity = $this->hasCentralObesity(
            $biometrics['waist_circumference'] ?? 0,
            $questionnaire['Q2'] ?? ''
        );
        $conditionCount = $this->countOtherConditions($questionnaire, $biometrics, $labTests);

        $hasMetabolicSyndrome = $hasCentralObesity && $conditionCount >= 2;

        return $hasMetabolicSyndrome ? 2 : 10;
    }

    protected function hasCentralObesity(float $waist, string $gender): bool
    {
        return $waist >= 95; // Iranian cut-off for both genders
    }

    protected function countOtherConditions(array $questionnaire, array $biometrics, array $labTests): int
    {
        $conditions = [
            $this->hasHighTriglycerides($labTests['triglycerides'] ?? 0),
            $this->hasLowHDL($labTests['hdl_cholesterol'] ?? 0, $questionnaire['Q2'] ?? ''),
            $this->hasHighBloodPressure(
                $biometrics['systolic_bp'] ?? 0,
                $biometrics['diastolic_bp'] ?? 0,
                $questionnaire['Q6'] ?? ''
            ),
            $this->hasHighGlucose($labTests['fbs'] ?? 0, $questionnaire['Q5'] ?? ''),
        ];

        return count(array_filter($conditions));
    }

    protected function hasHighTriglycerides(float $triglycerides): bool
    {
        return $triglycerides >= 150;
    }

    protected function hasLowHDL(float $hdl, string $gender): bool
    {
        return ($gender === 'male' && $hdl < 40) || ($gender === 'female' && $hdl < 50);
    }

    protected function hasHighBloodPressure(float $sbp, float $dbp, string $bpMeds): bool
    {
        return $sbp >= 130 || $dbp >= 85 || $bpMeds === 'B';
    }

    protected function hasHighGlucose(float $fbs, string $highGlucose): bool
    {
        return $fbs >= 100 || $highGlucose === 'B';
    }
}
