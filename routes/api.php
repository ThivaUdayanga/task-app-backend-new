<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('register', [AuthController::class, "register"]);
Route::post('login', [AuthController::class, "login"]);

Route::group([
    "middleware" => ["auth:sanctum"]
], function(){
    Route::get('profile', [AuthController::class, "profile"]);
    Route::post('logout', [AuthController::class, "logout"]);

    Route::apiResource('tasks', TaskController::class);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
