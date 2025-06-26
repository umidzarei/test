<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the ZeeWork API'
    ], 200);

})->where('any', '.*');


// health check with /up
Route::get('/up', function () {
    // TODO add some checks for sure API is up
    return response()->json([
        'message' => 'ZeeWork API is up and running'
    ], 200);
})->where('any', '.*');
