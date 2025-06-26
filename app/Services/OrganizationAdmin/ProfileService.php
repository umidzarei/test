<?php

namespace App\Services\OrganizationAdmin;

use App\Models\OrganizationAdmin;
use App\Repositories\OrganizationAdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    protected OrganizationAdminRepository $adminRepository;

    public function __construct(OrganizationAdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     *
     * @param OrganizationAdmin $admin
     * @return OrganizationAdmin
     */
    public function getAdminProfile(OrganizationAdmin $admin): OrganizationAdmin
    {
        return $admin;
    }

    /**
     *
     * @param OrganizationAdmin $admin
     * @param array $data
     * @return OrganizationAdmin
     */
    public function updateAdminProfile(OrganizationAdmin $admin, array $data): OrganizationAdmin
    {
        return $this->adminRepository->update($admin->id, $data);
    }

    /**
     *
     * @param OrganizationAdmin $admin
     * @param array $data
     * @return bool
     * @throws ValidationException
     */
    public function changeAdminPassword(OrganizationAdmin $admin, array $data): bool
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
