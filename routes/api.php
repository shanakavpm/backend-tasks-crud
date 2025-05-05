<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function (): void {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware(['auth:sanctum'])->group(function (): void {

    Route::resource('tasks', TaskController::class);
    Route::post('logout', [AuthController::class, 'logout']);

});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
