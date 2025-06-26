<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'                    => $this->faker->company,
            'national_id'             => $this->faker->unique()->numerify('##########'),
            'reg_number'              => $this->faker->unique()->numerify('#####'),
            'economic_code'           => $this->faker->unique()->numerify('##########'),
            'logo'                    => null,
            'address'                 => $this->faker->address,
            'company_phone'           => $this->faker->phoneNumber,
            'representative_name'     => $this->faker->name,
            'representative_position' => $this->faker->jobTitle,
            'representative_phone'    => $this->faker->phoneNumber,
        ];
    }
}
