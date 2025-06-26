<?php

namespace App\Services\HealthQuotient;

class CVDRiskService
{
    protected $framinghamTables; // Assume this is injected or loaded

    public function __construct()
    {
        // Load Framingham tables (e.g., from config or database)
        $this->framinghamTables = $this->loadFraminghamTables();
    }

    /**
     * Calculate CVD Risk Score (Framingham-based).
     *
     * @param array $questionnaire
     * @param array $biometrics
     * @param array $labTests
     * @return float
     */
    public function calculateScore(array $questionnaire, array $biometrics, array $labTests): float
    {
        $totalPoints = $this->calculateFraminghamPoints($questionnaire, $biometrics, $labTests);
        $riskPercentage = $this->convertToRiskPercentage($totalPoints, $questionnaire['Q2'] ?? '');

        return $this->mapToSegmentScore($riskPercentage);
    }

    protected function calculateFraminghamPoints(array $questionnaire, array $biometrics, array $labTests): int
    {
        $effectiveAge = max(30, $questionnaire['Q1'] ?? 0);
        $gender = $questionnaire['Q2'] ?? '';
        $isSmoker = ($questionnaire['Q12'] ?? '') === 'D';
        $treatedBP = ($questionnaire['Q6'] ?? '') === 'B';
        $hasDiabetes = ($questionnaire['Q5'] ?? '') === 'B' || ($labTests['fbs'] ?? 0) >= 126;

        $points = 0;
        $points += $this->getPointsFromTable('age', $effectiveAge, $gender);
        $points += $this->getPointsFromTable('total_cholesterol', $labTests['total_cholesterol'] ?? 0, $gender, $effectiveAge);
        $points += $this->getPointsFromTable('hdl_cholesterol', $labTests['hdl_cholesterol'] ?? 0, $gender);
        $points += $this->getPointsFromTable('sbp', $biometrics['systolic_bp'] ?? 0, $gender, $treatedBP);
        $points += $this->getPointsFromTable('smoking', $isSmoker, $gender, $effectiveAge);
        $points += $this->getPointsFromTable('diabetes', $hasDiabetes, $gender);

        return $points;
    }

    protected function getPointsFromTable(string $factor, $value, string $gender, $extra = null): int
    {
        // Placeholder: Implement lookup in Framingham tables
        // Example: $this->framinghamTables[$factor][$gender][$range]
        return 0; // Replace with actual table lookup
    }

    protected function convertToRiskPercentage(int $points, string $gender): float
    {
        // Placeholder: Implement conversion using Framingham risk table
        return 0; // Replace with actual conversion
    }

    protected function mapToSegmentScore(float $riskPercentage): float
    {
        if ($riskPercentage < 5) return 10;
        if ($riskPercentage < 7.5) return 9;
        if ($riskPercentage < 10) return 8;
        if ($riskPercentage < 15) return 6;
        if ($riskPercentage < 20) return 4;
        if ($riskPercentage < 30) return 2;
        return 0;
    }

    protected function loadFraminghamTables(): array
    {
        // Load or define Framingham tables
        return []; // Replace with actual tables
    }
}
