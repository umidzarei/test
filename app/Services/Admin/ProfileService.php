<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    protected AdminRepository $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     *
     * @param Admin $admin
     * @return Admin
     */
    public function getAdminProfile(Admin $admin): Admin
    {
        return $admin;
    }

    /**
     *
     * @param Admin $admin
     * @param array $data
     * @return Admin
     */
    public function updateAdminProfile(Admin $admin, array $data): Admin
    {
        return $this->adminRepository->update($admin->id, $data);
    }

    /**
     *
     * @param Admin $admin
     * @param array $data
     * @return bool
     * @throws ValidationException
     */
    public function changeAdminPassword(Admin $admin, array $data): bool
    {
        if (!Hash::check($data['current_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('passwords.current_password_incorrect')],
            ]);
        }

        $newPasswordHash = Hash::make($data['password']);
        $this->adminRepository->update($admin->id, ['password' => $newPasswordHash]);

        return true;
    }
}
