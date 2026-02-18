<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MediaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events/join', [EventController::class, 'join']);
    Route::apiResource('events', EventController::class);
    Route::apiResource('events.media', MediaController::class)->only(['index', 'store']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);
});
