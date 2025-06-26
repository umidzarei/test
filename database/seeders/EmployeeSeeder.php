<?php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\OrganizationEmployee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::with('departments')->get();

        if ($organizations->isEmpty()) {
            $this->command->info('No organizations (with departments) found. Please run OrganizationSeeder first. Skipping EmployeeSeeder.');
            return;
        }

        $testEmployee = Employee::factory()->create([
            'name'          => 'کارمند تست',
            'email'         => 'employee@example.com',
            'phone'         => '09909066113',
            'national_code' => '0000000000',
        ]);

        $firstOrganization = Organization::first();
        if ($firstOrganization && $firstOrganization->departments()->exists()) {
            $orgEmp = OrganizationEmployee::factory()->create([
                'employee_id'     => $testEmployee->id,
                'organization_id' => $firstOrganization->id,
                'job_position'    => 'توسعه‌دهنده ارشد',
            ]);
            $orgEmp->departments()->attach($firstOrganization->departments->first()->id);
        }
    }
}
