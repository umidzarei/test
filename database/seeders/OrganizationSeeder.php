<?php
namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::factory(1)->create()->each(function ($org) {
            Department::factory(2)->create([
                'organization_id' => $org->id,
            ]);
        });
    }
}
