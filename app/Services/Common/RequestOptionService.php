<?php

namespace App\Services\Common;

use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Auth;

class RequestOptionService
{
    protected SettingRepository $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function getOptionsForCurrentUser(): array
    {
        $user = Auth::user();

        $defaultSetting = $this->settingRepository->findByKey('default-request-options');
        $availableScores = [];
        if ($defaultSetting && is_array($defaultSetting->value)) {
            $availableScores = array_keys($defaultSetting->value);
        }

        $physicianFeedbackAvailable = false;
        if ($user instanceof \App\Models\Admin || $user instanceof \App\Models\OrganizationAdmin) {
            $physicianFeedbackAvailable = true;
        }

        return [
            'available_scores' => $availableScores,
            'physician_feedback_available' => $physicianFeedbackAvailable,
        ];
    }
}
