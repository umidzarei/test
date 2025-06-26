<?php
namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationAdmin;
use Illuminate\Database\Seeder;

class OrganizationAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::all()->each(function ($org) {
            OrganizationAdmin::factory()->create([
                'organization_id' => $org->id,
                'phone'           => '09909066113',
            ]);
        });
    }
}
