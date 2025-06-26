<?php

namespace App\Services\Physician;

use App\Models\Physician;
use App\Repositories\PhysicianRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    protected PhysicianRepository $physicianRepository;

    public function __construct(PhysicianRepository $physicianRepository)
    {
        $this->physicianRepository = $physicianRepository;
    }

    /**
     *
     * @param Physician $admin
     * @return Physician
     */
    public function getAdminProfile(Physician $admin): Physician
    {
        return $admin;
    }

    /**
     *
     * @param Physician $admin
     * @param array $data
     * @return Physician
     */
    public function updateAdminProfile(Physician $admin, array $data): Physician
    {
        return $this->physicianRepository->update($admin->id, $data);
    }

    /**
     *
     * @param Physician $admin
     * @param array $data
     * @return bool
     * @throws ValidationException
     */
    public function changeAdminPassword(Physician $admin, array $data): bool
    {
        if (!Hash::check($data['current_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('passwords.current_password_incorrect')],
            ]);
        }

        $newPasswordHash = Hash::make($data['password']);
        $this->physicianRepository->update($admin->id, ['password' => $newPasswordHash]);

        return true;
    }
}
