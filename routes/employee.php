<?php

use App\Http\Controllers\Employee\Organization\OrganizationController;
use App\Http\Controllers\Employee\Profile\ProfileController;
use App\Http\Controllers\Employee\Request\RequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'auth.guard:employee'])->prefix('employee')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/organizations', [OrganizationController::class, 'index']);

    Route::apiResource('requests', RequestController::class)->only(['store', 'index', 'show']);
});
