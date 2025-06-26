<?php
namespace Database\Factories;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\OrganizationEmployee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationEmployee>
 */
class OrganizationEmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrganizationEmployee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobPositions = ['کارشناس', 'مدیر میانی', 'کارمند اداری', 'توسعه‌دهنده', 'تحلیلگر'];
        return [
            'employee_id'     => Employee::factory(),
            'organization_id' => Organization::factory(),
            'job_position'    => $this->faker->randomElement($jobPositions),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
}
