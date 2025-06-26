<?php
namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name'       => 'super-admin',
            'guard_name' => 'admin',
        ]);
        $perm = Permission::firstOrCreate([
            'name'       => 'manage-users',
            'guard_name' => 'admin',
        ]);
        $role->givePermissionTo($perm);

        $admin = Admin::firstOrCreate([
            'email' => 'test@test.com',
        ], [
            'name'     => 'مدیر اصلی',
            'phone'    => '+989030907396',

            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($role);

    }
}
