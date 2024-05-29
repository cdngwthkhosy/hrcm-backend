<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Company Controller
Route::get('/company', [CompanyController::class, 'get_data']);
Route::post('/company', [CompanyController::class, 'create'])->middleware('auth:sanctum');


// Auth Controller
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('user', [AuthController::class, 'fetch'])->middleware('auth:sanctum');
