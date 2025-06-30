<?php

namespace App\Services\HealthQuotient;

use App\Services\HealthQuotient\Data\FraminghamData;

class CVDRiskService
{
    public function calculateScore(array $questionnaire, array $biometrics, array $labTests): float
    {
        $totalPoints = $this->calculateFraminghamPoints($questionnaire, $biometrics, $labTests);
        $gender = strtolower($questionnaire['Q2'] ?? 'male');
        $riskPercentage = $this->convertToRiskPercentage($totalPoints, $gender);
        return $this->mapToSegmentScore($riskPercentage);
    }

    protected function calculateFraminghamPoints(array $questionnaire, array $biometrics, array $labTests): int
    {
        $gender = strtolower($questionnaire['Q2'] ?? 'male');
        $isSmoker = ($questionnaire['Q12'] ?? 'A') === 'D';
        $hasDiabetes = ($questionnaire['Q5'] ?? 'A') === 'B';
        $treatedBP = ($questionnaire['Q6'] ?? 'A') === 'B';
        $systolicBPFactor = $treatedBP ? "Systolic BP (mmHg) - Treated" : "Systolic BP (mmHg) - Not Treated";

        $points = 0;
        $points += FraminghamData::getPoints("Age", (int) ($questionnaire['Q1'] ?? 30), $gender);
        $points += FraminghamData::getPoints("Smoker", $isSmoker, $gender);
        $points += FraminghamData::getPoints("Diabetes", $hasDiabetes, $gender);
        $points += FraminghamData::getPoints("Total Cholesterol (mmol/L)", (float) ($labTests['total_cholesterol'] ?? 0), $gender);
        $points += FraminghamData::getPoints("HDL-C (mmol/L)", (float) ($labTests['hdl'] ?? 0), $gender);
        $points += FraminghamData::getPoints($systolicBPFactor, (int) ($biometrics['systolic_bp'] ?? 0), $gender);

        return $points;
    }

    protected function convertToRiskPercentage(int $points, string $gender): float
    {
        return FraminghamData::getRiskPercentage($points, $gender);
    }

    protected function mapToSegmentScore(float $riskPercentage): float
    {
        if ($riskPercentage < 5.0)
            return 10.0;
        if ($riskPercentage < 7.5)
            return 9.0;
        if ($riskPercentage < 10.0)
            return 8.0;
        if ($riskPercentage < 15.0)
            return 6.0;
        if ($riskPercentage < 20.0)
            return 4.0;
        if ($riskPercentage < 30.0)
            return 2.0;
        return 0.0;
    }
}
