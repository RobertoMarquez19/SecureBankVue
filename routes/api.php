<?php

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

Route::post('cliente/register', [\App\Http\Controllers\API\ClienteController::class, 'store']);
Route::post('usuario/register', [\App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('usuario/login', [\App\Http\Controllers\API\AuthController::class, 'login']);

Route::middleware('auth:api')->group( function () {

});
