<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
}); 



Route::middleware(['auth:sanctum'])->group(function () {

    Route::resource('tasks', TaskController::class);
    Route::post('logout', [AuthController::class, 'logout']);
   
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
