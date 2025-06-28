<?php

namespace App\Services\Admin;

use App\Models\Admin;
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


    public function createRequest(Admin $admin, array $validatedData)
    {
        $employeeIds = $this->resolveEmployeeIds($validatedData);

        $options = $this->mergeRequestOptions($validatedData['options'] ?? []);

        $requestData = [
            'organization_id' => $validatedData['organization_id'],
            'occupational_medicine_id' => $validatedData['occupational_medicine_id'],
            'requester_id' => $admin->id,
            'requester_type' => get_class($admin),
            'status' => 'in_process',
        ];

        return $this->requestRepository->createWithDetails(
            $requestData,
            $employeeIds,
            Arr::get($validatedData, 'physician_ids', []),
            $options
        );
    }


    private function resolveEmployeeIds(array $data): array
    {
        $employeeIds = [];
        $organizationId = $data['organization_id'];

        if (!empty($data['target_all_employees'])) {
            $allOrgEmployees = $this->employeeRepository->getAllIdsByOrganization($organizationId);
            $employeeIds = array_merge($employeeIds, $allOrgEmployees);
        }

        if (!empty($data['target_department_ids'])) {
            $departmentEmployees = $this->employeeRepository->getByDepartmentIds($data['target_department_ids'], $organizationId)->pluck('id');
            $employeeIds = array_merge($employeeIds, $departmentEmployees->toArray());
        }

        if (!empty($data['target_employee_ids'])) {
            $employeeIds = array_merge($employeeIds, $data['target_employee_ids']);
        }

        return array_unique($employeeIds);
    }

    private function mergeRequestOptions(array $userOptions): array
    {
        $defaultSetting = $this->settingRepository->findByKey('default-request-options');
        $defaultScores = $defaultSetting ? collect($defaultSetting->value)->where('value', true)->keys()->toArray() : [];

        $selectedScores = $userOptions['selected_scores'] ?? [];

        $finalScores = array_unique(array_merge($defaultScores, $selectedScores));

        return [
            'selected_scores' => $finalScores,
            'physician_feedback_required' => $userOptions['physician_feedback_required'] ?? false,
        ];
    }
}
