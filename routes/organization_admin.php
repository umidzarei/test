<?php

use App\Http\Controllers\OrganizationAdmin\Department\DepartmentController;
use App\Http\Controllers\OrganizationAdmin\Employee\EmployeeController;
use App\Http\Controllers\OrganizationAdmin\Profile\ProfileController;
use App\Http\Controllers\OrganizationAdmin\Request\RequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'auth.guard:organization_admin'])->prefix('hr')->name('hr.')->group(function () {
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);
    Route::apiResource('requests', RequestController::class)->only(['store']);
});
