<?php
namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationAdmin>
 */
class OrganizationAdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name'            => $this->faker->name,
            'email'           => $this->faker->unique()->safeEmail,
            'password'        => bcrypt('password'),
            'phone'           => $this->faker->phoneNumber,
        ];
    }
}
