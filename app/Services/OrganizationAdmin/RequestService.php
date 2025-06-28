<?php

namespace App\Services\OrganizationAdmin;

use App\Models\OrganizationAdmin;
use App\Repositories\EmployeeRepository;
use App\Repositories\RequestRepository;
use App\Repositories\SettingRepository;
use Illuminate\Support\Arr;

class RequestService
{
    protected RequestRepository $requestRepository;
    protected EmployeeRepository $employeeRepository;
    protected SettingRepository $settingRepository;

    public function __construct(
        RequestRepository $requestRepository,
        EmployeeRepository $employeeRepository,
        SettingRepository $settingRepository
    ) {
        $this->requestRepository = $requestRepository;
        $this->employeeRepository = $employeeRepository;
        $this->settingRepository = $settingRepository;
    }


    public function createRequest(OrganizationAdmin $orgAdmin, array $validatedData)
    {
        $employeeIds = $this->resolveEmployeeIds($orgAdmin->organization_id, $validatedData);

        $options = $this->mergeRequestOptions(Arr::get($validatedData, 'options', []));

        $requestData = [
            'organization_id' => $orgAdmin->organization_id,
            'requester_id' => $orgAdmin->id,
            'requester_type' => get_class($orgAdmin),
            'status' => 'pending_admin_approval',
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
}
