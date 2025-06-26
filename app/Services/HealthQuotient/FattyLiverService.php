<?php

namespace App\Services\HealthQuotient;

class FattyLiverService
{
    /**
     * Calculate Fatty Liver Disease (FLD) Health Score.
     *
     * @param array $questionnaire
     * @param array $biometrics
     * @param array $labTests
     * @return float
     */
    public function calculateScore(array $questionnaire, array $biometrics, array $labTests): float
    {
        $alcoholRisk = $this->calculateAlcoholRisk($questionnaire);
        $metabolicRisk = $this->calculateMetabolicSteatosisRisk($questionnaire, $biometrics, $labTests);
        $liverEnzymeAlert = $this->calculateLiverEnzymeAlert($labTests);

        $overallRisk = $this->determineOverallFLDRisk($alcoholRisk, $metabolicRisk, $liverEnzymeAlert, $questionnaire, $labTests);

        return $this->mapToFLDHealthScore($overallRisk);
    }

    protected function calculateAlcoholRisk(array $questionnaire): string
    {
        $q14 = $questionnaire['Q14'] ?? '';
        $q15 = $questionnaire['Q15'] ?? '';
        $q2 = $questionnaire['Q2'] ?? '';

        $drinksPerWeek = match ($q14) {
            'A' => 0,
            'B' => 0.23,
            'C' => ($q15 === 'B' ? 1.0 : 1.5),
            'D' => ($q15 === 'B' ? 3.75 : 4.5),
            'E' => ($q15 === 'B' ? 7.5 : 9.0),
            default => 0,
        };

        $gramsPerDay = ($drinksPerWeek * 10) / 7;

        if ($gramsPerDay === 0) {
            return 'Low/None';
        }

        $isMale = $q2 === 'male';
        if (($isMale && $gramsPerDay > 30) || (!$isMale && $gramsPerDay > 20)) {
            return 'High';
        }

        return 'Moderate';
    }

    protected function calculateMetabolicSteatosisRisk(array $questionnaire, array $biometrics, array $labTests): string
    {
        $alt = $labTests['alt'] ?? 0;
        $ast = $labTests['ast'] ?? 0;
        $bmi = $biometrics['bmi'] ?? 0;
        $q2 = $questionnaire['Q2'] ?? '';
        $q5 = $questionnaire['Q5'] ?? '';
        $fbs = $labTests['fbs'] ?? 0;

        if ($ast === 0) {
            return 'Data Insufficient';
        }

        $hasT2DM = $q5 === 'B' || $fbs >= 126;
        $isFemale = $q2 === 'female';

        $altAstRatio = $alt / $ast;
        $hsi = (8 * $altAstRatio) + $bmi;
        if ($hasT2DM)
            $hsi += 2;
        if ($isFemale)
            $hsi += 2;

        if ($hsi < 30)
            return 'Low';
        if ($hsi <= 36)
            return 'Intermediate';
        return 'High';
    }

    protected function calculateLiverEnzymeAlert(array $labTests): string
    {
        $alt = $labTests['alt'] ?? 0;
        $ast = $labTests['ast'] ?? 0;
        $ulnAlt = 40; // Adjust based on lab reference
        $ulnAst = 40; // Adjust based on lab reference

        $altStatus = $alt <= $ulnAlt ? 'Normal' :
            ($alt <= 2 * $ulnAlt ? 'Mildly Elevated' : 'Significantly Elevated');
        $astStatus = $ast <= $ulnAst ? 'Normal' :
            ($ast <= 2 * $ulnAst ? 'Mildly Elevated' : 'Significantly Elevated');

        if ($altStatus === 'Normal' && $astStatus === 'Normal') {
            return 'None';
        }
        if ($altStatus === 'Significantly Elevated' || $astStatus === 'Significantly Elevated') {
            return 'Significant';
        }
        return 'Mild';
    }

    protected function determineOverallFLDRisk(
        string $alcoholRisk,
        string $metabolicRisk,
        string $liverEnzymeAlert,
        array $questionnaire,
        array $labTests
    ): string {
        $q5 = $questionnaire['Q5'] ?? '';
        $fbs = $labTests['fbs'] ?? 0;
        $hasT2DM = $q5 === 'B' || $fbs >= 126;

        // High Risk Conditions
        if (
            $alcoholRisk === 'High' ||
            $liverEnzymeAlert === 'Significant' ||
            ($alcoholRisk === 'Moderate' && $metabolicRisk === 'High') ||
            ($alcoholRisk === 'Moderate' && $liverEnzymeAlert === 'Mild') ||
            ($alcoholRisk === 'Low/None' && $metabolicRisk === 'High' && in_array($liverEnzymeAlert, ['Mild', 'Significant'])) ||
            ($alcoholRisk === 'Low/None' && $metabolicRisk === 'High' && $hasT2DM)
        ) {
            return 'High';
        }

        // Moderate Risk Conditions
        if (
            $alcoholRisk === 'Moderate' ||
            ($alcoholRisk === 'Low/None' && $metabolicRisk === 'High') ||
            ($alcoholRisk === 'Low/None' && $metabolicRisk === 'Intermediate' && in_array($liverEnzymeAlert, ['Mild', 'Significant'])) ||
            ($alcoholRisk === 'Low/None' && $metabolicRisk === 'Intermediate' && $hasT2DM) ||
            ($alcoholRisk === 'Low/None' && $liverEnzymeAlert === 'Mild')
        ) {
            return 'Moderate';
        }

        // Handle Data Insufficient
        if ($metabolicRisk === 'Data Insufficient') {
            if ($alcoholRisk === 'Low/None' && $liverEnzymeAlert === 'None') {
                return 'Indeterminate';
            }
            if ($alcoholRisk === 'High' || $liverEnzymeAlert === 'Significant') {
                return 'High';
            }
            if ($alcoholRisk === 'Moderate' || $liverEnzymeAlert === 'Mild') {
                return 'Moderate';
            }
        }

        return 'Low';
    }

    protected function mapToFLDHealthScore(string $overallRisk): float
    {
        return match ($overallRisk) {
            'Low' => 10,
            'Moderate' => 5,
            'High' => 1,
            'Indeterminate' => 4,
            default => 4,
        };
    }
}
