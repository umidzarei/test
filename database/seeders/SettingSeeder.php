<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settingsData = [
            [
                'key' => 'default-request-options',
                'value' => [
                    'diabetes_risk' => true,
                    'metabolic_syndrome' => true,
                    'cvd_risk' => true,
                    'mental_health' => true,
                    'lifestyle' => true,
                    'fatty_liver' => true,
                ],
            ],
        ];

        foreach ($settingsData as $settingData) {
            Setting::create($settingData);
        }
    }
}
