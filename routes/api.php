<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Task endpoints are consumed by Blade views in the same app,
    // so we use the normal session-based web guard.
    Route::apiResource('tasks', TaskController::class);
});