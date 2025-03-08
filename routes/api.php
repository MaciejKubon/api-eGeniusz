<?php

use App\Http\Controllers\api\Auth\AuthController;
use Illuminate\Support\Facades\Route;


//auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//subjectLevel

