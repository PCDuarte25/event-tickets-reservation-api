<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes for event and reservation management.
 *
 * Defines endpoints for handling events and their reservations.
 */

// Routes for managing events.
Route::apiResource('events', EventController::class);

// Routes for managing reservations within a specific event.
Route::prefix('events/{event}')->group(function () {
    Route::apiResource('reservations', ReservationController::class);
});
