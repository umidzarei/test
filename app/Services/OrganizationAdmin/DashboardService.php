<?php

namespace App\Services\OrganizationAdmin;

use App\Repositories\DepartmentRepository;
use App\Repositories\RequestRepository;
use App\Models\Request;

class DashboardService
{
    protected RequestRepository $requestRepository;
    protected DepartmentRepository $departmentRepository;


    public function __construct(RequestRepository $requestRepository, DepartmentRepository $departmentRepository)
    {
        $this->requestRepository = $requestRepository;
        $this->departmentRepository = $departmentRepository;
    }


    public function getDashboardData(int $organizationId): array
    {
        $participationRate = $this->calculateParticipationRate($organizationId);

        $latestCompletedRequest = $this->requestRepository->findLatestCompletedForOrganization($organizationId);

        $healthScoresSummary = $this->getHealthScoresSummary($latestCompletedRequest);
        $teamsStatus = $this->getTeamsStatus($latestCompletedRequest, $organizationId);

        return [
            'participation_rate' => $participationRate,
            'health_scores_summary' => $healthScoresSummary,
            'teams_status' => $teamsStatus,
        ];
    }


    private function calculateParticipationRate(int $organizationId): array
    {
        $latestActiveRequest = $this->requestRepository->findLatestActiveForOrganization($organizationId);

        if (!$latestActiveRequest || $latestActiveRequest->total_employees_count === 0) {
            return [
                'percentage' => 0,
                'description' => 'نرخ مشارکت (ارزیابی فعالی وجود ندارد)',
            ];
        }

        $percentage = round(
            ($latestActiveRequest->completed_employees_count / $latestActiveRequest->total_employees_count) * 100
        );

        return [
            'percentage' => $percentage,
            'description' => 'نرخ مشارکت در ارزیابی‌ها',
        ];
    }

    private function getHealthScoresSummary(?Request $request): array
    {
        $scoreKeys = [
            'hq_score' => 'امتیاز کلی HQ',
            'cvd_risk' => 'ریسک قلبی و عروقی',
            'metabolic_syndrome_risk' => 'سندروم متابولیک',
            'mental_health_score' => 'سلامت روان',
        ];
        if (!$request) {
            $summary = [];
            foreach ($scoreKeys as $key => $label) {
                $summary[$key] = ['value' => 0, 'label' => $label];
            }
            return $summary;
        }

        $allScores = $request->requestEmployees->pluck('calculatedScore')->filter();

        if ($allScores->isEmpty()) {
            $summary = [];
            foreach ($scoreKeys as $key => $label) {
                $summary[$key] = ['value' => 0, 'label' => $label];
            }
            return $summary;
        }

        $summary = [];
        foreach ($scoreKeys as $key => $label) {
            $summary[$key] = [
                'value' => round($allScores->avg($key), 1),
                'label' => $label,
            ];
        }

        return $summary;
    }


    private function getTeamsStatus(?Request $request, int $organizationId): array
    {
        if (!$request) {
            $departments = $this->departmentRepository->getForOrganization($organizationId);
            return $departments->map(function ($department) {
                return [
                    'team_name' => $department->name,
                    'hq_score' => 0,
                    'status' => 'بدون داده',
                ];
            })->toArray();
        }

        $employeesGroupedByDepartment = $request->requestEmployees->flatMap(function ($requestEmployee) {
            if ($requestEmployee->employee && $requestEmployee->employee->organizationEmployee->first()) {
                $departments = $requestEmployee->employee->organizationEmployee->first()->departments;
                return $departments->map(function ($department) use ($requestEmployee) {
                    return [
                        'department_name' => $department->name,
                        'department_id' => $department->id,
                        'score_record' => $requestEmployee->calculatedScore
                    ];
                });
            }
            return [];
        })->groupBy('department_name');

        $teamsStatus = [];
        foreach ($employeesGroupedByDepartment as $departmentName => $entries) {
            $departmentScores = $entries->pluck('score_record')->filter();

            if ($departmentScores->isNotEmpty()) {
                $averageHq = round($departmentScores->avg('hq_score'));
                $teamsStatus[] = [
                    'team_name' => $departmentName,
                    'hq_score' => $averageHq,
                    'status' => $this->getScoreStatusLabel($averageHq),
                ];
            }
        }

        return $teamsStatus;
    }


    private function getScoreStatusLabel(int $score): string
    {
        if ($score >= 90)
            return 'عالی';
        if ($score >= 75)
            return 'خوب';
        if ($score >= 60)
            return 'متوسط';
        return 'نیاز به توجه';
    }
}
