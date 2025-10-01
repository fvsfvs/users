<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

// Users
Route::get('users', [UserController::class, 'index']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::post('users', [UserController::class, 'store'])->middleware('auth.api:sanctum');
Route::put('users/{id}', [UserController::class, 'update'])->middleware('auth.api:sanctum');
Route::delete('users/{id}', [UserController::class, 'destroy'])->middleware('auth.api:sanctum');

// Auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth.api:sanctum');
