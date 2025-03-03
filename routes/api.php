<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', LoginController::class);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/tasks/datatable', [TaskController::class, 'datatable']);
    Route::apiResource('tasks', TaskController::class);

});
