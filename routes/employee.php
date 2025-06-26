<?php

use App\Http\Controllers\Employee\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'auth.guard:employee'])->prefix('employee')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});
