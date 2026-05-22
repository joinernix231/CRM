<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['api.session', 'api.token'])->group(function () {
    // Users
    Route::get('/user', [AuthController::class, 'user']);

    // System
    Route::post('/logout', [AuthController::class, 'logout']);

    // Clients
    Route::apiResource('clients', ClientController::class);
    // Contacts
    Route::apiResource('clients.contacts', ContactController::class);
});
