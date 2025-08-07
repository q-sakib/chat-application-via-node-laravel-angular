<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{

  AuthController,
  MessageController
};
// use App\Http\Controllers\AuthController;

// ✅ Public Auth Routes
Route::prefix('auth')->group(function () {
  Route::post('/register', [AuthController::class, 'register']);
  Route::post('/login', [AuthController::class, 'login']);
  Route::post('/refresh', [AuthController::class, 'refresh']);
});
Route::get('/users', [AuthController::class, 'allUsers']);

// ✅ Protected Routes (Requires Access Token with Auto Refresh)
Route::middleware(['auth:sanctum', 'auto-refresh'])->group(function () {
  Route::get('/me', [AuthController::class, 'user']);
  // Route::get('/users', [AuthController::class, 'allUsers']);
  Route::post('/logout', [AuthController::class, 'logout']);

  // Messaging Routes
  Route::post('/messages/send', [MessageController::class, 'send']);
  Route::get('/messages/conversation/{userId}', [MessageController::class, 'conversation']);
});