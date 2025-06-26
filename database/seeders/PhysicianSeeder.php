<?php
namespace Database\Seeders;

use App\Models\Physician;
use Illuminate\Database\Seeder;

class PhysicianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Physician::factory(1)->create([
            'phone' => '09909066113',
        ]);
    }
}
