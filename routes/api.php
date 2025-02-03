<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;


// Routes for managing events.
Route::apiResource('events', EventController::class);
