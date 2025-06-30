<?php

namespace App\Services\HealthQuotient\Data;

use InvalidArgumentException;


class FraminghamData
{

    private static array $pointsRules = [
        [
            "Risk Factor" => "Age",
            "Categories" => [
                ["Value / Range" => "30-34", "Points - Men" => 0, "Points - Women" => 0],
                ["Value / Range" => "35-39", "Points - Men" => 2, "Points - Women" => 2],
                ["Value / Range" => "40-44", "Points - Men" => 5, "Points - Women" => 4],
                ["Value / Range" => "45-49", "Points - Men" => 7, "Points - Women" => 5],
                ["Value / Range" => "50-54", "Points - Men" => 8, "Points - Women" => 7],
                ["Value / Range" => "55-59", "Points - Men" => 10, "Points - Women" => 8],
                ["Value / Range" => "60-64", "Points - Men" => 11, "Points - Women" => 9],
                ["Value / Range" => "65-69", "Points - Men" => 12, "Points - Women" => 10],
                ["Value / Range" => "70-74", "Points - Men" => 14, "Points - Women" => 11],
                ["Value / Range" => "75+", "Points - Men" => 15, "Points - Women" => 12]
            ]
        ],
        [
            "Risk Factor" => "Diabetes",
            "Categories" => [
                ["Value / Range" => "No", "Points - Men" => 0, "Points - Women" => 0],
                ["Value / Range" => "Yes", "Points - Men" => 3, "Points - Women" => 4]
            ]
        ],
        [
            "Risk Factor" => "HDL-C (mmol/L)",
            "Categories" => [
                ["Value / Range" => ">1.6", "Points - Men" => -2, "Points - Women" => -2],
                ["Value / Range" => "1.3-1.6", "Points - Men" => -1, "Points - Women" => -1],
                ["Value / Range" => "1.2-1.29", "Points - Men" => 0, "Points - Women" => 0],
                ["Value / Range" => "0.9-1.19", "Points - Men" => 1, "Points - Women" => 1],
                ["Value / Range" => "<0.9", "Points - Men" => 2, "Points - Women" => 2]
            ]
        ],
        [
            "Risk Factor" => "Smoker",
            "Categories" => [
                ["Value / Range" => "No", "Points - Men" => 0, "Points - Women" => 0],
                ["Value / Range" => "Yes", "Points - Men" => 4, "Points - Women" => 3]
            ]
        ],
        [
            "Risk Factor" => "Systolic BP (mmHg) - Not Treated",
            "Categories" => [
                ["Value / Range" => "<120", "Points - Men" => -2, "Points - Women" => -3],
                ["Value / Range" => "120-129", "Points - Men" => 0, "Points - Women" => 0],
                ["Value / Range" => "130-139", "Points - Men" => 1, "Points - Women" => 1],
                ["Value / Range" => "140-149", "Points - Men" => 2, "Points - Women" => 2],
                ["Value / Range" => "150-159", "Points - Men" => 2, "Points - Women" => 4],
                ["Value / Range" => "160+", "Points - Men" => 3, "Points - Women" => 5]
            ]
        ],
        [
            "Risk Factor" => "Systolic BP (mmHg) - Treated",
            "Categories" => [
                ["Value / Range" => "<120", "Points - Men" => 0, "Points - Women" => -1],
                ["Value / Range" => "120-129", "Points - Men" => 2, "Points - Women" => 2],
                ["Value / Range" => "130-139", "Points - Men" => 3, "Points - Women" => 3],
                ["Value / Range" => "140-149", "Points - Men" => 4, "Points - Women" => 5],
                ["Value / Range" => "150-159", "Points - Men" => 4, "Points - Women" => 6],
                ["Value / Range" => "160+", "Points - Men" => 5, "Points - Women" => 7]
            ]
        ],
        [
            "Risk Factor" => "Total Cholesterol (mmol/L)",
            "Categories" => [
                ["Value / Range" => "<4.1", "Points - Men" => 0, "Points - Women" => 0],
                ["Value / Range" => "4.1-5.19", "Points - Men" => 0, "Points - Women" => 1],
                ["Value / Range" => "5.2-6.19", "Points - Men" => 1, "Points - Women" => 3],
                ["Value / Range" => "6.2-7.2", "Points - Men" => 2, "Points - Women" => 4],
                ["Value / Range" => ">7.2", "Points - Men" => 3, "Points - Women" => 5]
            ]
        ]
    ];

    private static array $riskPercentageTable = [
        ["Total Points" => "-3 or less", "Men" => "<1", "Women" => "<1"],
        ["Total Points" => "-2", "Men" => "<1", "Women" => "1"],
        ["Total Points" => "-1", "Men" => "1.1", "Women" => "1.2"],
        ["Total Points" => "0", "Men" => "1.4", "Women" => "1.5"],
        ["Total Points" => "1", "Men" => "1.6", "Women" => "1.7"],
        ["Total Points" => "2", "Men" => "1.9", "Women" => "2"],
        ["Total Points" => "3", "Men" => "2.3", "Women" => "2.4"],
        ["Total Points" => "4", "Men" => "2.8", "Women" => "2.8"],
        ["Total Points" => "5", "Men" => "3.3", "Women" => "3.3"],
        ["Total Points" => "6", "Men" => "3.9", "Women" => "3.8"],
        ["Total Points" => "7", "Men" => "4.7", "Women" => "4.5"],
        ["Total Points" => "8", "Men" => "5.6", "Women" => "5.3"],
        ["Total Points" => "9", "Men" => "6.7", "Women" => "6.3"],
        ["Total Points" => "10", "Men" => "7.9", "Women" => "7.3"],
        ["Total Points" => "11", "Men" => "9.4", "Women" => "8.6"],
        ["Total Points" => "12", "Men" => "11.2", "Women" => "10"],
        ["Total Points" => "13", "Men" => "13.3", "Women" => "11.7"],
        ["Total Points" => "14", "Men" => "15.6", "Women" => "13.7"],
        ["Total Points" => "15", "Men" => "18.4", "Women" => "15.9"],
        ["Total Points" => "16", "Men" => "21.6", "Women" => "18.5"],
        ["Total Points" => "17", "Men" => "25.3", "Women" => "21.5"],
        ["Total Points" => "18", "Men" => "29.4", "Women" => "24.8"],
        ["Total Points" => "19", "Men" => ">30", "Women" => "27.5"],
        ["Total Points" => "20", "Men" => ">30", "Women" => ">30"],
        ["Total Points" => "21+", "Men" => ">30", "Women" => ">30"]
    ];

    public static function getPoints(string $factorName, $value, string $gender): int
    {
        $factorRules = null;
        foreach (self::$pointsRules as $rule) {
            if ($rule['Risk Factor'] === $factorName) {
                $factorRules = $rule['Categories'];
                break;
            }
        }
        if ($factorRules === null)
            throw new InvalidArgumentException("Invalid risk factor: {$factorName}");

        $pointsKey = ($gender === 'male') ? 'Points - Men' : 'Points - Women';
        foreach ($factorRules as $category) {
            if (self::isValueInRange($value, $category['Value / Range']))
                return $category[$pointsKey];
        }
        return 0;
    }

    public static function getRiskPercentage(int $totalPoints, string $gender): float
    {
        $riskKey = ($gender === 'male') ? 'Men' : 'Women';
        $riskColumn = "10-Year CVD Risk (%) - {$riskKey}";

        foreach (self::$riskPercentageTable as $row) {
            if (self::arePointsInRange($totalPoints, $row['Total Points'])) {
                $riskValue = $row[$riskColumn];
                if (strpos($riskValue, '<') === 0)
                    return 0.9;
                if (strpos($riskValue, '>') === 0)
                    return 30.1;
                return (float) $riskValue;
            }
        }
        return 30.1;
    }

    private static function isValueInRange($value, string $range): bool
    {
        if (in_array($range, ['Yes', 'No']))
            return ($range === 'Yes') === $value;
        if (strpos($range, '>') === 0)
            return (float) $value > (float) substr($range, 1);
        if (strpos($range, '<') === 0)
            return (float) $value < (float) substr($range, 1);
        if (strpos($range, '+') !== false)
            return (float) $value >= (float) rtrim($range, '+');
        if (strpos($range, '-') !== false) {
            [$min, $max] = explode('-', $range);
            return (float) $value >= (float) $min && (float) $value <= (float) $max;
        }
        return false;
    }

    private static function arePointsInRange(int $points, string $range): bool
    {
        if (strpos($range, 'or less') !== false)
            return $points <= (int) str_replace(' or less', '', $range);
        if (strpos($range, '+') !== false)
            return $points >= (int) rtrim($range, '+');
        return $points === (int) $range;
    }
}
