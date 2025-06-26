<?php

namespace App\Http\Controllers;

use App\Services\HealthQuotient\HealthQuotientService;
use Illuminate\Http\Request;

class HealthQuotientController extends Controller
{
    protected $healthQuotientService;

    public function __construct(HealthQuotientService $healthQuotientService)
    {
        $this->healthQuotientService = $healthQuotientService;
    }

    public function calculate(Request $request)
    {
        $inputData = [
            'questionnaire' => [
                'Q1' => 45, // Age
                'Q2' => 'male', // Gender
                'Q3' => 'A', // Family History
                // ... other questions
            ],
            'biometrics' => [
                'height' => 170, // cm
                'weight' => 70, // kg
                'bmi' => 70 / (1.7 * 1.7), // Calculated
                'waist_circumference' => 90, // cm
                'systolic_bp' => 120, // mmHg
                'diastolic_bp' => 80, // mmHg
            ],
            'lab_tests' => [
                'fbs' => 95, // mg/dL
                'total_cholesterol' => 180, // mg/dL
                'hdl_cholesterol' => 50, // mg/dL
                'triglycerides' => 140, // mg/dL
                'alt' => 30, // U/L
                'ast' => 25, // U/L
            ],
        ];

        try {
            $result = $this->healthQuotientService->calculateHealthQuotient($inputData);
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
