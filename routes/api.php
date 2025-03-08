<?php

use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\Subject\subjectLevelController;
use Illuminate\Support\Facades\Route;


//auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//subjectLevel
Route::middleware('auth:sanctum')->post('/subjectLevel', [subjectLevelController::class, 'store']);
Route::middleware('auth:sanctum')->get('/subjectLevel/{subjectLevel}', [subjectLevelController::class, 'show']);
Route::middleware('auth:sanctum')->get('/subjectLevel', [subjectLevelController::class, 'index']);
Route::middleware('auth:sanctum')->put('/subjectLevel/{subjectLevel}', [subjectLevelController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/subjectLevel/{subjectLevel}', [subjectLevelController::class, 'destroy']);
