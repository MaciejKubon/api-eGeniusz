<?php

use App\Http\Controllers\api\Auth\AuthController;
use App\Http\Controllers\api\Subject\subjectController;
use App\Http\Controllers\api\Subject\subjectLevelController;
use App\Http\Controllers\api\lessonController;
use App\Http\Controllers\api\termController;
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


//subject
Route::middleware('auth:sanctum')->post('/subject', [subjectController::class, 'store']);
Route::middleware('auth:sanctum')->get('/subject/{subject}', [subjectController::class, 'show']);
Route::middleware('auth:sanctum')->get('/subject', [subjectController::class, 'index']);
Route::middleware('auth:sanctum')->put('/subject/{subject}', [subjectController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/subject/{subject}', [subjectController::class, 'destroy']);


//lesson
Route::middleware('auth:sanctum')->post('/lesson', [lessonController::class, 'store']);
Route::middleware('auth:sanctum')->get('/lesson', [lessonController::class, 'index']);
Route::middleware('auth:sanctum')->get('/lesson/{lesson}', [lessonController::class, 'show']);
Route::middleware('auth:sanctum')->get('/teacherLesson/{user}', [lessonController::class, 'showTeacherLessons']);
Route::middleware('auth:sanctum')->put('/lesson/{lesson}', [lessonController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/lesson/{lesson}', [lessonController::class, 'destroy']);


//term
Route::middleware('auth:sanctum')->get('/term', [termController::class, 'index']);
Route::middleware('auth:sanctum')->get('/teacherTerm/{user}', [termController::class, 'showTeacherTerms']);
Route::middleware('auth:sanctum')->get('/term/{term}', [termController::class, 'show']);
Route::middleware('auth:sanctum')->post('/term', [termController::class, 'store']);
Route::middleware('auth:sanctum')->put('/term', [termController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/term/{term}', [termController::class, 'destroy']);

