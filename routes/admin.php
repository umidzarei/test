<?php
use App\Http\Controllers\Admin\Admin\AdminController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Organization\DepartmentController;
use App\Http\Controllers\Admin\Organization\OrganizationAdminController;
use App\Http\Controllers\Admin\Organization\OrganizationController;
use App\Http\Controllers\Admin\Physician\PhysicianController;
use App\Http\Controllers\Admin\Profile\ProfileController;
use App\Http\Controllers\Admin\OccupationalMedicine\OccupationalMedicineController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'auth.guard:admin'])->prefix('admin')->group(function () {
    Route::apiResource('admins', AdminController::class);
    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('organization-admins', OrganizationAdminController::class);
    Route::post('organizations/{organization}/employees/import', [EmployeeController::class, 'import'])->name('admin.organizations.employees.import');
    Route::apiResource('employees', EmployeeController::class);
    Route::delete(
        'employees/destroy-from-organization/{organizationId}/{employeeId}',
        [EmployeeController::class, 'destroyFromOrganization']
    );
    Route::post('physicians/import', [PhysicianController::class, 'import'])->name('admin.physicians.import');
    Route::apiResource('physicians', PhysicianController::class);
    Route::post('occupational-medicines/import', [OccupationalMedicineController::class, 'import'])->name('admin.occupational-medicines.import');
    Route::apiResource('occupational-medicines', OccupationalMedicineController::class);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);
});
