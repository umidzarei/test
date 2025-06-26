<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'auth.guard:physician'])->prefix(prefix: 'physician')->group(function () {

});
