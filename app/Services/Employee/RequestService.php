<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Repositories\RequestRepository;
use App\Repositories\SettingRepository;
use Illuminate\Support\Arr;
use App\Repositories\TebKarRepository;
class RequestService
{
    protected RequestRepository $requestRepository;
    protected SettingRepository $settingRepository;
    protected TebKarRepository $tebKarRepository;
    public function __construct(
        RequestRepository $requestRepository,
        SettingRepository $settingRepository,
        TebKarRepository $tebKarRepository
    ) {
        $this->requestRepository = $requestRepository;
        $this->settingRepository = $settingRepository;
        $this->tebKarRepository = $tebKarRepository;
    }


    public function createPersonalRequest(Employee $employee, array $validatedData)
    {
        $options = $this->mergeRequestOptions(Arr::get($validatedData, 'options', []));

        $requestData = [
            'organization_id' => $validatedData['organization_id'],
            'requester_id' => $employee->id,
            'requester_type' => get_class($employee),
            'status' => 'in_process',
        ];

        $employeeIds = [$employee->id];

        $physicianIds = [];

        return $this->requestRepository->createWithDetails(
            $requestData,
            $employeeIds,
            $physicianIds,
            $options
        );
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
            'physician_feedback_required' => false,
        ];
    }

    public function getRequestList(int $employeeId, array $filters)
    {
        return $this->requestRepository->listForEmployee($employeeId, $filters);
    }

    public function getRequestDetailsForEmployee(int $requestId, int $employeeId): array
    {
        $request = $this->requestRepository->getDetailsForEmployee($requestId, $employeeId);
        if (!$request) {
            return [
                'request' => null,
                'task_status' => []
            ];
        }
        $requestEmployee = $request->requestEmployees->first();
        $hraStatus = $requestEmployee?->hraQuestionnaireInstance?->status === 'submitted' ? 'completed' : 'pending';
        $labDataStatus = $requestEmployee?->labData ? 'completed' : 'pending';
        $tebKarStatus = 'pending_self_declaration';
        if ($requestEmployee?->tebKar) {
            $tebKarStatus = 'completed';
        } else {
            $latestValidTebKar = $this->tebKarRepository->findLatestValidForEmployee($employeeId);
            if ($latestValidTebKar) {
                $tebKarStatus = 'completed_from_history';
            }
        }
        $taskStatus = [
            'hra' => $hraStatus,
            'lab_data' => $labDataStatus,
            'teb_kar' => $tebKarStatus,
        ];
        return [
            'request' => $request,
            'task_status' => $taskStatus,
        ];
    }
}
