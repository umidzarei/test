<?php

use App\Http\Controllers\Common\Auth\AuthController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/admin.php';
require __DIR__ . '/employee.php';
require __DIR__ . '/organization_admin.php';
require __DIR__ . '/physician.php';

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('otp', [AuthController::class, 'otp']);
    Route::post('validate', [AuthController::class, 'validate']);
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
});
