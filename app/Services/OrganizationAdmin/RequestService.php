<?php

namespace App\Services\OrganizationAdmin;

use App\Models\CalculatedScores;
use App\Models\OrganizationAdmin;
use App\Models\RequestEmployee;
use App\Repositories\EmployeeRepository;
use App\Repositories\RequestRepository;
use App\Repositories\SettingRepository;
use App\Repositories\TebKarRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Repositories\RequestEmployeeRepository;
class RequestService
{
    protected RequestRepository $requestRepository;
    protected EmployeeRepository $employeeRepository;
    protected SettingRepository $settingRepository;
    protected TebKarRepository $tebKarRepository;
    protected RequestEmployeeRepository $requestEmployeeRepository;
    public function __construct(
        RequestRepository $requestRepository,
        EmployeeRepository $employeeRepository,
        SettingRepository $settingRepository,
        TebKarRepository $tebKarRepository,
        RequestEmployeeRepository $requestEmployeeRepository

    ) {
        $this->requestRepository = $requestRepository;
        $this->employeeRepository = $employeeRepository;
        $this->settingRepository = $settingRepository;
        $this->tebKarRepository = $tebKarRepository;
        $this->requestEmployeeRepository = $requestEmployeeRepository;
    }

    public function getRequestListForOrganization(int $organizationId, array $params)
    {
        $requests = $this->requestRepository->listForOrganization($organizationId, $params);

        $requests->getCollection()->transform(function ($request) {
            if ($request->total_employees_count > 0) {
                $request->participation_rate = round(($request->completed_employees_count / $request->total_employees_count) * 100);
            } else {
                $request->participation_rate = 0;
            }
            return $request;
        });

        return $requests;
    }
    public function createRequest(OrganizationAdmin $orgAdmin, array $validatedData)
    {
        $employeeIds = $this->resolveEmployeeIds($orgAdmin->organization_id, $validatedData);

        $options = $this->mergeRequestOptions(Arr::get($validatedData, 'options', []));

        $requestData = [
            'organization_id' => $orgAdmin->organization_id,
            'requester_id' => $orgAdmin->id,
            'requester_type' => get_class($orgAdmin),
            'status' => 'pending',
            'occupational_medicine_id' => null,
        ];

        $physicianIds = [];

        return $this->requestRepository->createWithDetails(
            $requestData,
            $employeeIds,
            $physicianIds,
            $options
        );
    }


    private function resolveEmployeeIds(int $organizationId, array $data): array
    {
        $employeeIds = [];
        if (!empty($data['target_all_employees'])) {
            $employeeIds = array_merge($employeeIds, $this->employeeRepository->getAllIdsByOrganization($organizationId));
        }
        if (!empty($data['target_department_ids'])) {
            $departmentEmployees = $this->employeeRepository->getByDepartmentIds($data['target_department_ids'], $organizationId)->pluck('id');
            $employeeIds = array_merge($employeeIds, $departmentEmployees->toArray());
        }
        if (!empty($data['target_employee_ids'])) {
            $employeeIds = array_merge($employeeIds, $data['target_employee_ids']);
        }
        return array_values(array_unique($employeeIds));
    }

    private function mergeRequestOptions(array $userOptions): array
    {
        $defaultSetting = $this->settingRepository->findByKey('default-request-options');
        $defaultScores = [];
        if ($defaultSetting && is_array($defaultSetting->value)) {
            $defaultScores = collect($defaultSetting->value)->filter()->keys()->toArray();
        }
        $selectedScores = Arr::get($userOptions, 'selected_scores', []);
        $finalScores = array_values(array_unique(array_merge($defaultScores, $selectedScores)));
        return [
            'selected_scores' => $finalScores,
            'physician_feedback_required' => Arr::get($userOptions, 'physician_feedback_required', false),
        ];
    }
    public function getRequestDetails(int $organizationId, int $requestId)
    {
        $request = $this->requestRepository->getDetailsForOrganization($organizationId, $requestId);

        if (!$request) {
            return null;
        }

        $request->participation_rate = ($request->total_employees_count > 0)
            ? round(($request->completed_employees_count / $request->total_employees_count) * 100)
            : 0;

        if ($request->status === 'done') {
            $request->aggregated_reports = $this->generateAggregatedReports($request->requestEmployees);
        } else {
            $request->requestEmployees->transform(function ($requestEmployee) {
                $requestEmployee->tasks_status = $this->determineParticipantTaskStatus($requestEmployee);
                return $requestEmployee;
            });
        }

        return $request;
    }
    private function determineParticipantTaskStatus(RequestEmployee $requestEmployee): array
    {
        $hraStatus = 'pending';
        if ($instance = $requestEmployee->hraQuestionnaireInstance) {
            $hraStatus = $instance->status;
        }
        $labDataStatus = $requestEmployee->labData ? 'completed' : 'pending';
        $tebKarStatus = 'pending';
        if ($requestEmployee->tebKar) {
            $tebKarStatus = 'completed';
        } else {
            if ($this->tebKarRepository->findLatestValidForEmployee($requestEmployee->employee_id)) {
                $tebKarStatus = 'completed_from_history';
            }
        }
        return [
            'hra' => $hraStatus,
            'lab_data' => $labDataStatus,
            'teb_kar' => $tebKarStatus,
        ];
    }
    private function generateAggregatedReports(Collection $requestEmployees): array
    {
        $allScores = $requestEmployees->pluck('calculatedScore')->filter();

        if ($allScores->isEmpty()) {
            return ['overall_score_averages' => [], 'department_score_averages' => []];
        }

        $scoreColumns = (new CalculatedScores())->getFillable();
        $scoreColumns = array_diff($scoreColumns, ['request_employee_id']);

        $overallAverages = [];
        foreach ($scoreColumns as $column) {
            $overallAverages[$column] = round($allScores->avg(str_replace(':', '', $column)), 2);
        }

        $departmentAverages = [];
        $employeesGroupedByDepartment = $requestEmployees->flatMap(function ($requestEmployee) {
            $departments = $requestEmployee->employee->organizationEmployee->first()->departments;
            return $departments->map(function ($department) use ($requestEmployee) {
                return ['department_name' => $department->name, 'score_record' => $requestEmployee->calculatedScore];
            });
        })->groupBy('department_name');

        foreach ($employeesGroupedByDepartment as $departmentName => $entries) {
            $departmentScores = $entries->pluck('score_record')->filter();
            if ($departmentScores->isNotEmpty()) {
                foreach ($scoreColumns as $column) {
                    $departmentAverages[$departmentName][str_replace(':', '', $column)] = round($departmentScores->avg(str_replace(':', '', $column)), 2);
                }
            }
        }

        return [
            'overall_score_averages' => $overallAverages,
            'department_score_averages' => $departmentAverages,
        ];
    }
    public function getPaginatedRequestEmployees(int $requestId, array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->requestEmployeeRepository->listForRequest($requestId, $params);
    }
}
