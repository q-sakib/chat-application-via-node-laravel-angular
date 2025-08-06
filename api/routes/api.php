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

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/messages/send', [MessageController::class, 'send']);
  Route::get('/messages/conversation/{userId}', [MessageController::class, 'conversation']);
});